<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/database.php';

use ClinicFlow\Services\BillingService;

requireAuth();

$pdo = getDB();
$billingService = new BillingService($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $inv = $billingService->getInvoiceById($id);
        if (!$inv) {
            http_response_code(404);
            echo json_encode(['error' => 'Invoice not found']);
            exit;
        }
        echo json_encode(['success' => true, 'data' => $inv]);
        exit;
    }

    $status = $_GET['status'] ?? null;
    $patientId = $_GET['patient_id'] ?? null;
    $invoices = $billingService->getInvoices($status, $patientId);
    echo json_encode(['success' => true, 'data' => $invoices]);
    exit;
}

if ($method === 'POST') {
    validateCsrfRequest();
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $action = $input['action'] ?? 'create';

    try {
        if ($action === 'pay') {
            $id = $input['id'] ?? null;
            $amount = (float)($input['amount_paid'] ?? 0);
            $method = $input['payment_method'] ?? 'Cash';
            if (!$id || $amount <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'id and valid amount_paid are required']);
                exit;
            }
            $inv = $billingService->recordPayment($id, $amount, $method);
            echo json_encode(['success' => true, 'data' => $inv]);
            exit;
        }

        $inv = $billingService->createInvoice($input, $input['items'] ?? []);
        echo json_encode(['success' => true, 'data' => $inv]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed.']);
