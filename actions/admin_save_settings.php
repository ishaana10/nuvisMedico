<?php
/**
 * Administrator Settings Save Action
 */
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin.php");
    exit;
}

$pdo = getDB();

$settings = [
    'clinic_name'      => trim($_POST['clinic_name'] ?? 'ClinicFlow Medical Center'),
    'clinic_subtitle'  => trim($_POST['clinic_subtitle'] ?? ''),
    'clinic_address'   => trim($_POST['clinic_address'] ?? ''),
    'clinic_phone'     => trim($_POST['clinic_phone'] ?? ''),
    'clinic_email'     => trim($_POST['clinic_email'] ?? ''),
    'clinic_dea'       => trim($_POST['clinic_dea'] ?? ''),
    'clinic_npi'       => trim($_POST['clinic_npi'] ?? ''),
    'rx_header_title'       => trim($_POST['rx_header_title'] ?? 'OFFICIAL MEDICAL PRESCRIPTION'),
    'rx_disclaimer'         => trim($_POST['rx_disclaimer'] ?? ''),
    'rx_footer_note'        => trim($_POST['rx_footer_note'] ?? ''),
    'invoice_header_title'  => trim($_POST['invoice_header_title'] ?? 'MEDICAL SERVICES INVOICE'),
    'invoice_tax_id'        => trim($_POST['invoice_tax_id'] ?? ''),
    'invoice_payment_terms' => trim($_POST['invoice_payment_terms'] ?? ''),
    'invoice_footer_note'   => trim($_POST['invoice_footer_note'] ?? ''),
    'receipt_header_title'  => trim($_POST['receipt_header_title'] ?? 'OFFICIAL PAYMENT RECEIPT'),
    'receipt_thank_you_msg' => trim($_POST['receipt_thank_you_msg'] ?? ''),
    'doc_prc_no'            => trim($_POST['doc_prc_no'] ?? ''),
    'doc_ptr_no'            => trim($_POST['doc_ptr_no'] ?? '')
];

$isSqlite = ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite');
if ($isSqlite) {
    $stmt = $pdo->prepare("INSERT INTO clinic_settings (setting_key, setting_value) VALUES (?, ?) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value");
} else {
    $stmt = $pdo->prepare("INSERT INTO clinic_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
}

foreach ($settings as $key => $val) {
    $stmt->execute([$key, $val]);
}

setToast("Settings Saved", "Clinic branding and prescription settings updated successfully.");
header("Location: ../admin.php");
exit;
