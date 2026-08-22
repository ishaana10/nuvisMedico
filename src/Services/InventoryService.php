<?php

namespace ClinicFlow\Services;

use PDO;
use ClinicFlow\Utils\Uuid;

class InventoryService {
    private PDO $db;
    private AuditService $audit;

    public function __construct(PDO $db, ?AuditService $audit = null) {
        $this->db = $db;
        $this->audit = $audit ?? new AuditService($db);
    }

    public function getInventoryItems(?string $category = null, bool $activeOnly = true, ?string $clinicId = null): array {
        $clinicId = $clinicId ?? ($_SESSION['clinic_id'] ?? 'default-clinic');
        $sql = "SELECT * FROM inventory WHERE (clinic_id = :cid OR clinic_id IS NULL)";
        $params = ['cid' => $clinicId];

        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }

        if ($category && $category !== 'All') {
            $sql .= " AND category = :cat";
            $params['cat'] = $category;
        }

        $sql .= " ORDER BY name ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItemById(string $id, ?string $clinicId = null): ?array {
        $clinicId = $clinicId ?? ($_SESSION['clinic_id'] ?? 'default-clinic');
        $stmt = $this->db->prepare("SELECT * FROM inventory WHERE id = :id AND (clinic_id = :cid OR clinic_id IS NULL)");
        $stmt->execute(['id' => $id, 'cid' => $clinicId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function addItem(array $data, ?string $clinicId = null): array {
        $clinicId = $clinicId ?? ($_SESSION['clinic_id'] ?? 'default-clinic');
        $id = Uuid::uuidv7();
        $sku = $data['sku'] ?? ('SKU-' . rand(10000, 99999));

        $stmt = $this->db->prepare(
            "INSERT INTO inventory (id, clinic_id, name, sku, category, current_stock, min_threshold, unit, status, last_restocked, cost_price, unit_price, batch_number, expiry_date, is_active, vms_tax_code, custom_fields) " .
            "VALUES (:id, :cid, :name, :sku, :category, :stock, :min_threshold, :unit, :status, :last_restocked, :cost_price, :unit_price, :batch_number, :expiry_date, 1, :vms_tax_code, :custom_fields)"
        );

        $stock = (int)($data['current_stock'] ?? 0);
        $minThreshold = (int)($data['min_threshold'] ?? 10);
        $status = $stock > $minThreshold ? 'In Stock' : ($stock > 0 ? 'Low Stock' : 'Out of Stock');

        $stmt->execute([
            'id' => $id,
            'cid' => $clinicId,
            'name' => $data['name'],
            'sku' => $sku,
            'category' => $data['category'] ?? 'General',
            'stock' => $stock,
            'min_threshold' => $minThreshold,
            'unit' => $data['unit'] ?? 'Box',
            'status' => $status,
            'last_restocked' => date('Y-m-d'),
            'cost_price' => (float)($data['cost_price'] ?? 0.0),
            'unit_price' => (float)($data['unit_price'] ?? 0.0),
            'batch_number' => $data['batch_number'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'vms_tax_code' => $data['vms_tax_code'] ?? 'A',
            'custom_fields' => isset($data['custom_fields']) ? json_encode($data['custom_fields']) : null
        ]);

        $this->audit->logInventoryChange($id, 'ADD_ITEM', $stock, ['name' => $data['name'], 'sku' => $sku]);

        return array_merge(['id' => $id, 'clinic_id' => $clinicId, 'sku' => $sku, 'status' => $status], $data);
    }

    public function restockItem(string $id, int $changeAmount, string $type = 'restock', ?string $supplier = null, float $unitCost = 0.0, ?string $notes = null, ?string $clinicId = null): array {
        $item = $this->getItemById($id, $clinicId);
        if (!$item) {
            throw new \RuntimeException("Inventory item not found.");
        }

        $prevStock = (int)$item['current_stock'];
        $newStock = $prevStock + $changeAmount;
        $minThreshold = (int)$item['min_threshold'];
        $status = $newStock > $minThreshold ? 'In Stock' : ($newStock > 0 ? 'Low Stock' : 'Out of Stock');

        $upd = $this->db->prepare(
            "UPDATE inventory SET current_stock = :stock, status = :status, last_restocked = :date WHERE id = :id"
        );
        $upd->execute([
            'stock' => $newStock,
            'status' => $status,
            'date' => date('Y-m-d'),
            'id' => $id
        ]);

        $logId = Uuid::uuidv7();
        $logStmt = $this->db->prepare(
            "INSERT INTO inventory_logs (id, clinic_id, inventory_id, change_amount, previous_stock, new_stock, type, supplier, unit_cost, notes, created_by, created_at) " .
            "VALUES (:id, :cid, :inv_id, :change, :prev, :new, :type, :supplier, :cost, :notes, :user, :at)"
        );

        $logStmt->execute([
            'id' => $logId,
            'cid' => $item['clinic_id'],
            'inv_id' => $id,
            'change' => $changeAmount,
            'prev' => $prevStock,
            'new' => $newStock,
            'type' => $type,
            'supplier' => $supplier,
            'cost' => $unitCost,
            'notes' => $notes,
            'user' => $_SESSION['user_name'] ?? 'System',
            'at' => date('Y-m-d H:i:s')
        ]);

        $this->audit->logInventoryChange($id, 'RESTOCK', $changeAmount, ['previous' => $prevStock, 'new' => $newStock]);

        return [
            'id' => $id,
            'previous_stock' => $prevStock,
            'new_stock' => $newStock,
            'status' => $status
        ];
    }
}
