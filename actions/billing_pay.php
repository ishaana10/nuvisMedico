<?php
/**
 * Mark Invoice Paid Handler
 */
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

$invoiceId = $_POST['invoice_id'] ?? '';

if ($invoiceId !== '') {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE invoices SET status = 'Paid', patient_owed = 0.00 WHERE id = ?");
    $stmt->execute([$invoiceId]);
    setToast("Invoice Paid", "Payment confirmed and receipt generated.");
}

header("Location: ../billing.php");
exit;
