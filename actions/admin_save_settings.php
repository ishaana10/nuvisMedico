<?php
/**
 * Administrator Settings Save Action
 */
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
    'rx_header_title'  => trim($_POST['rx_header_title'] ?? 'OFFICIAL MEDICAL PRESCRIPTION'),
    'rx_disclaimer'    => trim($_POST['rx_disclaimer'] ?? ''),
    'rx_footer_note'   => trim($_POST['rx_footer_note'] ?? '')
];

$stmt = $pdo->prepare("INSERT INTO clinic_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

foreach ($settings as $key => $val) {
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $sqStmt = $pdo->prepare("INSERT INTO clinic_settings (setting_key, setting_value) VALUES (?, ?) ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value");
        $sqStmt->execute([$key, $val]);
    } else {
        $stmt->execute([$key, $val]);
    }
}

setToast("Settings Saved", "Clinic branding and prescription settings updated successfully.");
header("Location: ../admin.php");
exit;
