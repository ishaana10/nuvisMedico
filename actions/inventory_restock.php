<?php
/**
 * Inventory Restock Handler (Quick & Detailed Restock)
 */
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security.php';

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

$itemId = $_POST['item_id'] ?? '';
$amount = (int)($_POST['amount'] ?? 0);
$restockType = $_POST['restock_type'] ?? 'quick'; // quick or detailed
$supplier = trim($_POST['supplier'] ?? 'Standard Supplier');
$unitCost = isset($_POST['unit_cost']) && $_POST['unit_cost'] !== '' ? (float)$_POST['unit_cost'] : null;
$notes = trim($_POST['notes'] ?? '');
$batchNumber = trim($_POST['batch_number'] ?? '');
$expiryDate = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

if ($itemId !== '' && $amount > 0) {
    // Get item
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ? AND is_active = 1");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();

    if ($item) {
        $prevStock = (int)$item['current_stock'];
        $newStock = $prevStock + $amount;
        $status = $newStock <= $item['min_threshold'] ? 'Low Stock' : 'In Stock';
        $today = date('Y-m-d');
        $userName = $_SESSION['user_name'] ?? 'Staff';

        $costToUse = $unitCost !== null ? $unitCost : (float)$item['cost_price'];

        // Update Inventory
        if (!empty($batchNumber) || !empty($expiryDate)) {
            $uStmt = $pdo->prepare("UPDATE inventory SET current_stock = ?, status = ?, last_restocked = ?, batch_number = COALESCE(NULLIF(?, ''), batch_number), expiry_date = COALESCE(?, expiry_date) WHERE id = ?");
            $uStmt->execute([$newStock, $status, $today, $batchNumber, $expiryDate, $itemId]);
        } else {
            $uStmt = $pdo->prepare("UPDATE inventory SET current_stock = ?, status = ?, last_restocked = ? WHERE id = ?");
            $uStmt->execute([$newStock, $status, $today, $itemId]);
        }

        // Create Restock Audit Log
        $logType = $restockType === 'detailed' ? 'detailed_restock' : 'quick_restock';
        $logNotes = !empty($notes) ? $notes : ($restockType === 'detailed' ? 'Detailed restock entry' : 'Quick stock increment');

        $logStmt = $pdo->prepare("INSERT INTO inventory_logs (id, inventory_id, change_amount, previous_stock, new_stock, type, supplier, unit_cost, notes, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $logStmt->execute([
            'log-' . uniqid(), $itemId, $amount, $prevStock, $newStock, $logType, $supplier, $costToUse, $logNotes, $userName, date('Y-m-d H:i:s')
        ]);

        setToast("Inventory Restocked", "Added $amount {$item['unit']} to {$item['name']}. New Stock: $newStock.");
    } else {
        setToast("Error", "Selected inventory item is inactive or not found.", "error");
    }
} else {
    setToast("Validation Error", "Invalid restock quantity.", "error");
}

header("Location: ../inventory.php");
exit;
