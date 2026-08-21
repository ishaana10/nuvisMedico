<?php
/**
 * Add New Inventory Item Action (Admin / Developer)
 */
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security.php';

$role = $_SESSION['user_role'] ?? '';
if (!in_array($role, ['Administrator', 'Developer', 'Staff', 'Doctor', 'Nurse'])) {
    // Only logged in staff can add, but let's check role if strict
}
if (!in_array($role, ['Administrator', 'Developer'])) {
    setToast("Access Denied", "Only Administrators and Developers can add new inventory items.", "error");
    header("Location: ../inventory.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../inventory.php");
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($csrf)) {
    setToast("Security Error", "Invalid CSRF token.", "error");
    header("Location: ../inventory.php");
    exit;
}

$pdo = getDB();

$name = trim($_POST['name'] ?? '');
$sku = trim($_POST['sku'] ?? '');
$category = trim($_POST['category'] ?? 'Pharmaceuticals');
$currentStock = (int)($_POST['current_stock'] ?? 0);
$minThreshold = (int)($_POST['min_threshold'] ?? 10);
$unit = trim($_POST['unit'] ?? 'Boxes');
$costPrice = (float)($_POST['cost_price'] ?? 0.00);
$unitPrice = (float)($_POST['unit_price'] ?? 0.00);
$batchNumber = trim($_POST['batch_number'] ?? '');
$expiryDate = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
$vmsTaxCode = trim($_POST['vms_tax_code'] ?? 'A');

// Custom fields JSON handling
$customFieldsInput = $_POST['custom_fields'] ?? [];
$customFieldsJson = is_array($customFieldsInput) ? json_encode($customFieldsInput) : null;

if (empty($name) || empty($sku)) {
    setToast("Validation Error", "Item Name and SKU are required fields.", "error");
    header("Location: ../inventory.php");
    exit;
}

// Check SKU uniqueness
$check = $pdo->prepare("SELECT id FROM inventory WHERE sku = ?");
$check->execute([$sku]);
if ($check->fetch()) {
    setToast("Duplicate SKU", "An inventory item with SKU '$sku' already exists.", "error");
    header("Location: ../inventory.php");
    exit;
}

$id = 'inv-' . uniqid();
$status = $currentStock <= $minThreshold ? 'Low Stock' : 'In Stock';
$today = date('Y-m-d');
$userName = $_SESSION['user_name'] ?? 'Admin';

$stmt = $pdo->prepare("INSERT INTO inventory (id, name, sku, category, current_stock, min_threshold, unit, status, last_restocked, cost_price, unit_price, batch_number, expiry_date, is_active, vms_tax_code, custom_fields) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)");
$stmt->execute([
    $id, $name, $sku, $category, $currentStock, $minThreshold, $unit, $status, $today, $costPrice, $unitPrice, $batchNumber, $expiryDate, $vmsTaxCode, $customFieldsJson
]);

// Create initial stock audit log
if ($currentStock > 0) {
    $logStmt = $pdo->prepare("INSERT INTO inventory_logs (id, inventory_id, change_amount, previous_stock, new_stock, type, supplier, unit_cost, notes, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $logStmt->execute([
        'log-' . uniqid(), $id, $currentStock, 0, $currentStock, 'initial', 'Initial Setup', $costPrice, 'Initial stock entry upon creation', $userName, date('Y-m-d H:i:s')
    ]);
}

setToast("Inventory Added", "Item '$name' ($sku) created successfully.");
header("Location: ../inventory.php");
exit;
