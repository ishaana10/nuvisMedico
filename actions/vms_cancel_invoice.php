<?php
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/autoloader.php';
require_once __DIR__ . '/../config/database.php';

use ClinicFlow\Services\VMSService;

validateCsrfRequest();

$invoiceId = trim($_POST['invoice_id'] ?? '');
if (empty($invoiceId)) {
    $_SESSION['flash_error'] = "Missing invoice ID for cancellation.";
    header("Location: ../billing.php");
    exit;
}

try {
    $pdo = getDB();
    $vmsService = new VMSService($pdo);

    $cashierName = $_SESSION['user_name'] ?? 'Admin';
    $cancelInvoiceId = $vmsService->cancelInvoice($invoiceId, $cashierName);

    $_SESSION['flash_success'] = "Invoice successfully cancelled and fiscalized cancellation document generated!";
} catch (Exception $e) {
    $_SESSION['flash_error'] = "Cancellation failed: " . $e->getMessage();
}

header("Location: ../billing.php");
exit;
