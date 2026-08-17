<?php
/**
 * Action: Medical Certificate Save / Issue
 */

require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../patients.php');
    exit();
}

$patientId = trim($_POST['patient_id'] ?? '');
$visitId = trim($_POST['visit_id'] ?? '');
$issueDate = trim($_POST['issue_date'] ?? date('Y-m-d'));
$diagnosis = trim($_POST['diagnosis'] ?? '');
$fitnessStatus = trim($_POST['fitness_status'] ?? 'Fit for Work / School');
$fitStatusDetails = trim($_POST['fit_status_details'] ?? '');
$recommendations = trim($_POST['recommendations'] ?? '');
$doctorName = trim($_POST['doctor_name'] ?? $_SESSION['user_name'] ?? 'Dr. Sarah Jenkins');
$prcNumber = trim($_POST['prc_number'] ?? '');
$ptrNumber = trim($_POST['ptr_number'] ?? '');

if (!$patientId || !$diagnosis) {
    setToast('Error', 'Patient ID and Diagnosis are required.', 'error');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../patients.php'));
    exit();
}

$pdo = getDB();

// Generate Certificate Number (MC-YYYYMMDD-XXXX)
$certSeq = sprintf("%04d", rand(1, 9999));
$certNum = 'MC-' . date('Ymd') . '-' . $certSeq;
$certId = 'mc-' . uniqid();

$stmt = $pdo->prepare("INSERT INTO medical_certificates (id, certificate_number, patient_id, visit_id, issue_date, diagnosis, fitness_status, fit_status_details, recommendations, doctor_name, prc_number, ptr_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $certId,
    $certNum,
    $patientId,
    $visitId ?: null,
    $issueDate,
    $diagnosis,
    $fitnessStatus,
    $fitStatusDetails,
    $recommendations,
    $doctorName,
    $prcNumber,
    $ptrNumber
]);

setToast('Success', 'Medical Certificate issued successfully.', 'success');

// Redirect to print page immediately or back to reference page
if (isset($_POST['print_immediately']) && $_POST['print_immediately'] === '1') {
    header('Location: ../print_medical_certificate.php?id=' . urlencode($certId));
} else {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../patient_detail.php?id=' . urlencode($patientId)));
}
exit();
