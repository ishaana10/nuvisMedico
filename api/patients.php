<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/database.php';

use ClinicFlow\Repositories\PatientRepository;
use ClinicFlow\Services\AuditService;

requireAuth();

$pdo = getDB();
$repo = new PatientRepository($pdo);
$audit = new AuditService($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    $search = $_GET['search'] ?? null;

    if ($id) {
        $patient = $repo->findById($id);
        if (!$patient) {
            http_response_code(404);
            echo json_encode(['error' => 'Patient not found']);
            exit;
        }
        echo json_encode(['success' => true, 'data' => $patient]);
        exit;
    }

    if ($search) {
        $patients = $repo->search($search);
    } else {
        $patients = $repo->findAllActive();
    }

    echo json_encode(['success' => true, 'data' => $patients]);
    exit;
}

if ($method === 'POST') {
    validateCsrfRequest();
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $id = $input['id'] ?? null;

    if ($id) {
        // Update
        $success = $repo->update($id, $input);
        if ($success) {
            $audit->log("UPDATE_PATIENT", "Updated patient $id");
            echo json_encode(['success' => true, 'data' => $repo->findById($id)]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to update patient']);
        }
        exit;
    }

    // Create
    if (empty($input['first_name']) || empty($input['last_name']) || empty($input['dob'])) {
        http_response_code(400);
        echo json_encode(['error' => 'First name, last name, and date of birth are required.']);
        exit;
    }

    $newPatient = $repo->create($input);
    $audit->log("CREATE_PATIENT", "Created patient {$newPatient['id']} - {$newPatient['first_name']} {$newPatient['last_name']}");
    echo json_encode(['success' => true, 'data' => $newPatient]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed.']);
