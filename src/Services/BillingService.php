<?php

namespace ClinicFlow\Services;

use PDO;
use ClinicFlow\Utils\Uuid;

class BillingService {
    private PDO $db;
    private AuditService $audit;

    public function __construct(PDO $db, ?AuditService $audit = null) {
        $this->db = $db;
        $this->audit = $audit ?? new AuditService($db);
    }

    public function getInvoices(?string $status = null, ?string $patientId = null, ?string $clinicId = null): array {
        $clinicId = $clinicId ?? ($_SESSION['clinic_id'] ?? 'default-clinic');
        $sql = "SELECT * FROM invoices WHERE (clinic_id = :cid OR clinic_id IS NULL)";
        $params = ['cid' => $clinicId];

        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        if ($patientId) {
            $sql .= " AND patient_id = :pid";
            $params['pid'] = $patientId;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInvoiceById(string $id, ?string $clinicId = null): ?array {
        $clinicId = $clinicId ?? ($_SESSION['clinic_id'] ?? 'default-clinic');
        $stmt = $this->db->prepare("SELECT * FROM invoices WHERE id = :id AND (clinic_id = :cid OR clinic_id IS NULL)");
        $stmt->execute(['id' => $id, 'cid' => $clinicId]);
        $inv = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inv) {
            return null;
        }

        $itemStmt = $this->db->prepare("SELECT * FROM invoice_items WHERE invoice_id = :id");
        $itemStmt->execute(['id' => $id]);
        $inv['items'] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        return $inv;
    }

    public function createInvoice(array $data, array $items = [], ?string $clinicId = null): array {
        $clinicId = $clinicId ?? ($_SESSION['clinic_id'] ?? 'default-clinic');
        $id = Uuid::uuidv7();
        $invNumber = 'INV-' . date('Y') . '-' . sprintf('%04d', rand(1, 9999));

        $totalAmount = 0.0;
        foreach ($items as $item) {
            $totalAmount += (float)($item['total_price'] ?? (($item['unit_price'] ?? 0) * ($item['quantity'] ?? 1)));
        }
        if ($totalAmount <= 0.0 && isset($data['amount'])) {
            $totalAmount = (float)$data['amount'];
        }

        $insuranceCovered = (float)($data['insurance_covered'] ?? 0.0);
        $patientOwed = max(0.0, $totalAmount - $insuranceCovered);

        $stmt = $this->db->prepare(
            "INSERT INTO invoices (id, clinic_id, invoice_number, patient_id, patient_name, patient_mrn, service_date, due_date, amount, status, insurance_covered, patient_owed, services, invoice_type, transaction_type, seller_tin, business_location, cashier) " .
            "VALUES (:id, :cid, :inv_num, :pid, :pname, :pmrn, :sdate, :ddate, :amt, :status, :ins, :owed, :services, :inv_type, :txn_type, :tin, :loc, :cashier)"
        );

        $stmt->execute([
            'id' => $id,
            'cid' => $clinicId,
            'inv_num' => $invNumber,
            'pid' => $data['patient_id'] ?? null,
            'pname' => $data['patient_name'] ?? 'Walk-in Patient',
            'pmrn' => $data['patient_mrn'] ?? 'N/A',
            'sdate' => $data['service_date'] ?? date('Y-m-d'),
            'ddate' => $data['due_date'] ?? date('Y-m-d', strtotime('+30 days')),
            'amt' => $totalAmount,
            'status' => 'Pending',
            'ins' => $insuranceCovered,
            'owed' => $patientOwed,
            'services' => json_encode($data['services'] ?? []),
            'inv_type' => $data['invoice_type'] ?? 'Normal',
            'txn_type' => $data['transaction_type'] ?? 'Sale',
            'tin' => $data['seller_tin'] ?? '502579006',
            'loc' => $data['business_location'] ?? 'Suva Central Clinic',
            'cashier' => $_SESSION['user_name'] ?? 'Admin'
        ]);

        foreach ($items as $item) {
            $itemId = Uuid::uuidv7();
            $itemStmt = $this->db->prepare(
                "INSERT INTO invoice_items (id, clinic_id, invoice_id, name, gtin, unit_price, quantity, total_price, tax_label, tax_rate, tax_amount) " .
                "VALUES (:id, :cid, :inv_id, :name, :gtin, :uprice, :qty, :tprice, :label, :rate, :tamt)"
            );
            $qty = (float)($item['quantity'] ?? 1.0);
            $uPrice = (float)($item['unit_price'] ?? 0.0);
            $tPrice = $qty * $uPrice;
            $taxRate = (float)($item['tax_rate'] ?? 15.0);
            $taxAmt = $tPrice * ($taxRate / 100.0);

            $itemStmt->execute([
                'id' => $itemId,
                'cid' => $clinicId,
                'inv_id' => $id,
                'name' => $item['name'] ?? 'Service',
                'gtin' => $item['gtin'] ?? null,
                'uprice' => $uPrice,
                'qty' => $qty,
                'tprice' => $tPrice,
                'label' => $item['tax_label'] ?? 'A',
                'rate' => $taxRate,
                'tamt' => $taxAmt
            ]);
        }

        $this->audit->log("CREATE_INVOICE", "Created invoice $invNumber for $" . number_format($totalAmount, 2), null, null, null, $clinicId);

        return $this->getInvoiceById($id, $clinicId);
    }

    public function recordPayment(string $invoiceId, float $amountPaid, string $paymentMethod = 'Cash', ?string $clinicId = null): array {
        $inv = $this->getInvoiceById($invoiceId, $clinicId);
        if (!$inv) {
            throw new \RuntimeException("Invoice not found.");
        }

        $currentOwed = (float)$inv['patient_owed'];
        $newOwed = max(0.0, $currentOwed - $amountPaid);
        $newStatus = $newOwed <= 0 ? 'Paid' : 'Partial';

        $stmt = $this->db->prepare("UPDATE invoices SET patient_owed = :owed, status = :status WHERE id = :id");
        $stmt->execute(['owed' => $newOwed, 'status' => $newStatus, 'id' => $invoiceId]);

        $this->audit->log("RECORD_PAYMENT", "Payment of $" . number_format($amountPaid, 2) . " via $paymentMethod for invoice {$inv['invoice_number']}", null, null, null, $clinicId);

        return $this->getInvoiceById($invoiceId, $clinicId);
    }
}
