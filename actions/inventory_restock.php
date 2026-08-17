<?php
/**
 * Inventory Restock Handler
 */
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

$itemId = $_POST['item_id'] ?? '';
$amount = (int)($_POST['amount'] ?? 0);

if ($itemId !== '' && $amount > 0) {
    $pdo = getDB();

    // Get item
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();

    if ($item) {
        $newStock = $item['current_stock'] + $amount;
        $status = $newStock <= $item['min_threshold'] ? 'Low Stock' : 'In Stock';
        $today = date('Y-m-d');

        $uStmt = $pdo->prepare("UPDATE inventory SET current_stock = ?, status = ?, last_restocked = ? WHERE id = ?");
        $uStmt->execute([$newStock, $status, $today, $itemId]);

        setToast("Inventory Restocked", "Added $amount units to stock.");
    }
}

header("Location: ../inventory.php");
exit;
