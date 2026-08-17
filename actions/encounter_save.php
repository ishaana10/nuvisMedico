<?php
/**
 * Clinical Encounter Save / Prescriptions / Finalize Handler
 */
require_once __DIR__ . '/../config/database.php';

$pdo = getDB();

$action = $_REQUEST['action'] ?? 'save';
$patientId = $_REQUEST['patient_id'] ?? '';

if ($patientId === '') {
    header("Location: ../patients.php");
    exit;
}

if ($action === 'delete_rx') {
    $rxId = $_GET['rx_id'] ?? '';
    if ($rxId !== '') {
        $stmt = $pdo->prepare("DELETE FROM prescriptions WHERE id = ? AND patient_id = ?");
        $stmt->execute([$rxId, $patientId]);
        setToast("Medication Removed", "Prescription line removed.", "info");
    }
    header("Location: ../clinical_visit.php?patient_id=$patientId");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_rx'])) {
        $medName = trim($_POST['medication_name'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $frequency = trim($_POST['frequency'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');

        if ($medName !== '') {
            $rxId = "rx-" . time();
            $stmt = $pdo->prepare("INSERT INTO prescriptions (id, patient_id, medication_name, dosage, frequency, duration, instructions) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$rxId, $patientId, $medName, $dosage, $frequency, $duration, $instructions]);
            setToast("Medication Added", "$medName $dosage added to prescription.");
        }
        header("Location: ../clinical_visit.php?patient_id=$patientId");
        exit;
    }

    if ($action === 'save' || $action === 'finish') {
        $bp = trim($_POST['blood_pressure'] ?? '120/80');
        $hr = (int)($_POST['heart_rate'] ?? 72);
        $temp = (float)($_POST['temperature'] ?? 98.6);
        $spo2 = (int)($_POST['oxygen_sat'] ?? 99);

        $subjective = trim($_POST['subjective'] ?? '');
        $objective = trim($_POST['objective'] ?? '');
        $icdCode = trim($_POST['icd_code'] ?? 'J01.90');
        $plan = trim($_POST['plan'] ?? '');

        // Update vitals
        $vStmt = $pdo->prepare("DELETE FROM vitals WHERE patient_id = ?");
        $vStmt->execute([$patientId]);

        $vInsert = $pdo->prepare("INSERT INTO vitals (id, patient_id, blood_pressure, heart_rate, temperature, oxygen_sat) VALUES (?, ?, ?, ?, ?, ?)");
        $vInsert->execute(["v-" . time(), $patientId, $bp, $hr, $temp, $spo2]);

        // Update SOAP Notes
        $sStmt = $pdo->prepare("DELETE FROM soap_notes WHERE patient_id = ?");
        $sStmt->execute([$patientId]);

        $assessmentJson = json_encode([['code' => $icdCode, 'label' => $icdCode]]);
        $sInsert = $pdo->prepare("INSERT INTO soap_notes (id, patient_id, subjective, objective, assessment_codes, plan) VALUES (?, ?, ?, ?, ?, ?)");
        $sInsert->execute(["s-" . time(), $patientId, $subjective, $objective, $assessmentJson, $plan]);

        if ($action === 'finish') {
            // Get patient name
            $pStmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $pStmt->execute([$patientId]);
            $patient = $pStmt->fetch();

            // Insert into past_visits
            $pvId = "pv-" . time();
            $pvInsert = $pdo->prepare("INSERT INTO past_visits (id, patient_id, visit_date, title, summary, doctor_name) VALUES (?, ?, ?, ?, ?, ?)");
            $pvInsert->execute([
                $pvId,
                $patientId,
                date('M d, Y'),
                "Clinical Encounter ($icdCode)",
                substr($plan, 0, 100) . "...",
                "Dr. Sarah Jenkins"
            ]);

            // Clear from queue
            $qStmt = $pdo->prepare("DELETE FROM queue WHERE patient_id = ?");
            $qStmt->execute([$patientId]);

            // Add activity
            $actStmt = $pdo->prepare("INSERT INTO activities (id, type, title, detail, timestamp, badge_type) VALUES (?, ?, ?, ?, ?, ?)");
            $actStmt->execute([
                "act-" . time(),
                "visit_completed",
                "Visit Completed: " . $patient['first_name'] . ' ' . $patient['last_name'],
                "Just now • Dr. Jenkins",
                "Just now",
                "blue"
            ]);

            setToast("Visit Finalized!", "Encounter for " . $patient['first_name'] . ' ' . $patient['last_name'] . " has been finalized.");
            header("Location: ../patient_detail.php?id=$patientId");
            exit;
        } else {
            setToast("Changes Saved", "Vitals and SOAP notes updated successfully.");
            header("Location: ../clinical_visit.php?patient_id=$patientId");
            exit;
        }
    }
}
