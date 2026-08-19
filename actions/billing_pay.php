<?php
/**
 * Mark Invoice Paid Handler with Custom Payments & CSRF
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security.php';

requireAuth();
validateCsrfRequest();

$invoiceId = $_POST['invoice_id'] ?? '';
$paymentAmount = (float)($_POST['payment_amount'] ?? 0);
$paymentMethod = $_POST['payment_method'] ?? 'Cash';

if ($invoiceId !== '') {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? LIMIT 1");
    $stmt->execute([$invoiceId]);
    $invoice = $stmt->fetch();

    if ($invoice) {
        $newOwed = max(0.00, (float)$invoice['patient_owed'] - ($paymentAmount > 0 ? $paymentAmount : (float)$invoice['patient_owed']));
        $newStatus = ($newOwed <= 0) ? 'Paid' : 'Partial';

        $updateStmt = $pdo->prepare("UPDATE invoices SET status = ?, patient_owed = ? WHERE id = ?");
        $updateStmt->execute([$newStatus, $newOwed, $invoiceId]);

        setToast("Payment Recorded", "Payment of $" . number_format($paymentAmount > 0 ? $paymentAmount : $invoice['patient_owed'], 2) . " via " . htmlspecialchars($paymentMethod) . " recorded.");
    }
}

header("Location: ../billing.php");
exit;
