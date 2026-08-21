<?php
/**
 * Edit Inventory Item Action (Admin / Developer)
 */
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security.php';

$role = $_SESSION['user_role'] ?? '';
if (!in_array($role, ['Administrator', 'Developer'])) {
    setToast("Access Denied", "Only Administrators and Developers can edit inventory items.", "error");
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

$itemId = trim($_POST['item_id'] ?? '');
$name = trim($_POST['name'] ?? '');
$sku = trim($_POST['sku'] ?? '');
$category = trim($_POST['category'] ?? 'Pharmaceuticals');
$minThreshold = (int)($_POST['min_threshold'] ?? 10);
$unit = trim($_POST['unit'] ?? 'Boxes');
$costPrice = (float)($_POST['cost_price'] ?? 0.00);
$unitPrice = (float)($_POST['unit_price'] ?? 0.00);
$batchNumber = trim($_POST['batch_number'] ?? '');
$expiryDate = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
$vmsTaxCode = trim($_POST['vms_tax_code'] ?? 'A');

$customFieldsInput = $_POST['custom_fields'] ?? [];
$customFieldsJson = is_array($customFieldsInput) ? json_encode($customFieldsInput) : null;

if (empty($itemId) || empty($name) || empty($sku)) {
    setToast("Validation Error", "Item ID, Name, and SKU are required.", "error");
    header("Location: ../inventory.php");
    exit;
}

// Fetch current item
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->execute([$itemId]);
$item = $stmt->fetch();

if (!$item) {
    setToast("Not Found", "Inventory item not found.", "error");
    header("Location: ../inventory.php");
    exit;
}

// Check SKU uniqueness if changed
if ($sku !== $item['sku']) {
    $check = $pdo->prepare("SELECT id FROM inventory WHERE sku = ? AND id != ?");
    $check->execute([$sku, $itemId]);
    if ($check->fetch()) {
        setToast("Duplicate SKU", "Another item with SKU '$sku' already exists.", "error");
        header("Location: ../inventory.php");
        exit;
    }
}

$status = $item['current_stock'] <= $minThreshold ? 'Low Stock' : 'In Stock';

$uStmt = $pdo->prepare("UPDATE inventory SET name = ?, sku = ?, category = ?, min_threshold = ?, unit = ?, status = ?, cost_price = ?, unit_price = ?, batch_number = ?, expiry_date = ?, vms_tax_code = ?, custom_fields = ? WHERE id = ?");
$uStmt->execute([
    $name, $sku, $category, $minThreshold, $unit, $status, $costPrice, $unitPrice, $batchNumber, $expiryDate, $vmsTaxCode, $customFieldsJson, $itemId
]);

setToast("Inventory Updated", "Item '$name' updated successfully.");
header("Location: ../inventory.php");
exit;
