<?php
/**
 * Delete Inventory Item Action (Admin / Developer)
 * Soft deletes item by setting is_active = 0
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
    setToast("Access Denied", "Only Administrators and Developers can delete inventory records.", "error");
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

if (!empty($itemId)) {
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch();

    if ($item) {
        // Soft delete item by marking is_active = 0
        $dStmt = $pdo->prepare("UPDATE inventory SET is_active = 0 WHERE id = ?");
        $dStmt->execute([$itemId]);

        // Audit Log
        $userName = $_SESSION['user_name'] ?? 'Admin';
        $logStmt = $pdo->prepare("INSERT INTO inventory_logs (id, inventory_id, change_amount, previous_stock, new_stock, type, notes, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $logStmt->execute([
            'log-' . uniqid(), $itemId, 0, $item['current_stock'], $item['current_stock'], 'deactivation', 'Soft deleted / deactivated item', $userName, date('Y-m-d H:i:s')
        ]);

        setToast("Item Deleted", "Inventory record '{$item['name']}' has been removed.", "info");
    }
}

header("Location: ../inventory.php");
exit;
