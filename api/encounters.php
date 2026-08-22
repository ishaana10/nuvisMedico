<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/database.php';

use ClinicFlow\Services\EncounterService;

requireAuth();

$pdo = getDB();
$encounterService = new EncounterService($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $patientId = $_GET['patient_id'] ?? null;
    if (!$patientId) {
        http_response_code(400);
        echo json_encode(['error' => 'patient_id is required']);
        exit;
    }

    $history = $encounterService->getEncountersByPatient($patientId);
    echo json_encode(['success' => true, 'data' => $history]);
    exit;
}

if ($method === 'POST') {
    validateCsrfRequest();
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $patientId = $input['patient_id'] ?? null;
    if (!$patientId) {
        http_response_code(400);
        echo json_encode(['error' => 'patient_id is required']);
        exit;
    }

    $vitals = $input['vitals'] ?? [];
    $soap = $input['soap'] ?? [];
    $prescriptions = $input['prescriptions'] ?? [];
    $finalize = !empty($input['finalize']);
    $visitId = $input['visit_id'] ?? null;

    try {
        $result = $encounterService->saveEncounter($patientId, $vitals, $soap, $prescriptions, $finalize, $visitId);
        echo json_encode(['success' => true, 'data' => $result]);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed.']);
