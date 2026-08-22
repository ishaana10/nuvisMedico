<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/database.php';

use ClinicFlow\Services\AppointmentService;

requireAuth();

$pdo = getDB();
$appService = new AppointmentService($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $app = $appService->getAppointmentById($id);
        if (!$app) {
            http_response_code(404);
            echo json_encode(['error' => 'Appointment not found']);
            exit;
        }
        echo json_encode(['success' => true, 'data' => $app]);
        exit;
    }

    $date = $_GET['date'] ?? null;
    $doctorId = $_GET['doctor_id'] ?? null;
    $patientId = $_GET['patient_id'] ?? null;

    $appointments = $appService->getAppointments($date, $doctorId, $patientId);
    echo json_encode(['success' => true, 'data' => $appointments]);
    exit;
}

if ($method === 'POST') {
    validateCsrfRequest();
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $action = $input['action'] ?? 'create';

    try {
        if ($action === 'update_status') {
            $id = $input['id'] ?? null;
            $status = $input['status'] ?? 'Scheduled';
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'id is required']);
                exit;
            }
            $res = $appService->updateStatus($id, $status);
            echo json_encode(['success' => $res, 'id' => $id, 'status' => $status]);
            exit;
        }

        if (empty($input['patient_id']) || empty($input['doctor_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'patient_id and doctor_id are required']);
            exit;
        }

        $app = $appService->createAppointment($input);
        echo json_encode(['success' => true, 'data' => $app]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed.']);
