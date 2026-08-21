<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/database.php';

use ClinicFlow\Services\InventoryService;

requireAuth();

$pdo = getDB();
$invService = new InventoryService($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $item = $invService->getItemById($id);
        if (!$item) {
            http_response_code(404);
            echo json_encode(['error' => 'Item not found']);
            exit;
        }
        echo json_encode(['success' => true, 'data' => $item]);
        exit;
    }

    $category = $_GET['category'] ?? null;
    $items = $invService->getInventoryItems($category);
    echo json_encode(['success' => true, 'data' => $items]);
    exit;
}

if ($method === 'POST') {
    validateCsrfRequest();
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $action = $input['action'] ?? 'add';

    try {
        if ($action === 'restock') {
            $id = $input['id'] ?? null;
            $amount = (int)($input['change_amount'] ?? 0);
            if (!$id || $amount === 0) {
                http_response_code(400);
                echo json_encode(['error' => 'id and non-zero change_amount are required']);
                exit;
            }
            $result = $invService->restockItem($id, $amount, $input['type'] ?? 'restock', $input['supplier'] ?? null, (float)($input['unit_cost'] ?? 0.0), $input['notes'] ?? null);
            echo json_encode(['success' => true, 'data' => $result]);
            exit;
        }

        if (empty($input['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Item name is required']);
            exit;
        }

        $item = $invService->addItem($input);
        echo json_encode(['success' => true, 'data' => $item]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed.']);
