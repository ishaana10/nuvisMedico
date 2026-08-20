<?php
/**
 * Clinical Encounter Save / Prescriptions / Finalize Handler
 */
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

$pdo = getDB();

$action = $_REQUEST['action'] ?? 'save';
$patientId = $_REQUEST['patient_id'] ?? '';
$visitId = $_REQUEST['visit_id'] ?? '';

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
    header("Location: ../clinical_visit.php?patient_id=$patientId&visit_id=$visitId");
    exit;
}

if ($action === 'copy_rx') {
    $rxId = $_GET['rx_id'] ?? '';
    if ($rxId !== '') {
        $stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE id = ? AND patient_id = ?");
        $stmt->execute([$rxId, $patientId]);
        $oldRx = $stmt->fetch();

        if ($oldRx) {
            $newRxId = "rx-" . time() . '-' . rand(100, 999);
            $copyStmt = $pdo->prepare("INSERT INTO prescriptions (id, patient_id, visit_id, medication_name, dosage, frequency, duration, instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $copyStmt->execute([
                $newRxId,
                $patientId,
                $visitId,
                $oldRx['medication_name'],
                $oldRx['dosage'],
                $oldRx['frequency'],
                $oldRx['duration'],
                $oldRx['instructions']
            ]);
            setToast("Medication Copied", "Copied " . $oldRx['medication_name'] . " to current encounter.");
        }
    }
    header("Location: ../clinical_visit.php?patient_id=$patientId&visit_id=$visitId");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create_invoice') {
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch();

        if ($patient) {
            $invId = "inv-" . time();
            $invNum = "INV-" . date('Y') . "-" . rand(1000, 9999);
            $serviceDesc = trim($_POST['service_description'] ?? 'Clinical Consultation & Examination');
            $amount = (float)($_POST['amount'] ?? 150.00);
            $insuranceCovered = (float)($_POST['insurance_covered'] ?? 0.00);
            $patientOwed = max(0.00, $amount - $insuranceCovered);
            $serviceDate = trim($_POST['service_date'] ?? date('Y-m-d'));
            $dueDate = trim($_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days')));

            $servicesJson = json_encode([$serviceDesc]);

            $invInsert = $pdo->prepare("INSERT INTO invoices (id, invoice_number, patient_name, patient_mrn, service_date, due_date, amount, status, insurance_covered, patient_owed, services) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $invInsert->execute([
                $invId,
                $invNum,
                $patient['first_name'] . ' ' . $patient['last_name'],
                $patient['mrn'],
                $serviceDate,
                $dueDate,
                $amount,
                'Pending',
                $insuranceCovered,
                $patientOwed,
                $servicesJson
            ]);

            setToast("Invoice Created", "Invoice $invNum generated for " . $patient['first_name'] . ' ' . $patient['last_name'] . " ($" . number_format($patientOwed, 2) . " owed).");
        }

        header("Location: ../clinical_visit.php?patient_id=$patientId&visit_id=$visitId");
        exit;
    }

    if ($action === 'edit_rx') {
        $rxId = trim($_POST['rx_id'] ?? '');
        $medName = trim($_POST['medication_name'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $frequency = trim($_POST['frequency'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');

        if ($rxId !== '' && $medName !== '') {
            $updateStmt = $pdo->prepare("UPDATE prescriptions SET medication_name = ?, dosage = ?, frequency = ?, duration = ?, instructions = ? WHERE id = ? AND patient_id = ?");
            $updateStmt->execute([$medName, $dosage, $frequency, $duration, $instructions, $rxId, $patientId]);
            setToast("Medication Updated", "Prescription line updated successfully.");
        }
        header("Location: ../clinical_visit.php?patient_id=$patientId&visit_id=$visitId");
        exit;
    }

    if (isset($_POST['add_rx'])) {
        $medName = trim($_POST['medication_name'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $frequency = trim($_POST['frequency'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');

        if ($medName !== '') {
            $rxId = "rx-" . time() . '-' . rand(100, 999);
            $stmt = $pdo->prepare("INSERT INTO prescriptions (id, patient_id, visit_id, medication_name, dosage, frequency, duration, instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$rxId, $patientId, $visitId, $medName, $dosage, $frequency, $duration, $instructions]);
            setToast("Medication Added", "$medName $dosage added to prescription.");
        }
        header("Location: ../clinical_visit.php?patient_id=$patientId&visit_id=$visitId");
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

            // Fetch current vitals
            $vitalsData = [
                'blood_pressure' => $bp,
                'heart_rate' => $hr,
                'temperature' => $temp,
                'oxygen_sat' => $spo2
            ];

            // Fetch current SOAP notes
            $soapData = [
                'subjective' => $subjective,
                'objective' => $objective,
                'assessment_code' => $icdCode,
                'plan' => $plan
            ];

            // Fetch prescriptions for current visit_id
            $rxStmt = $pdo->prepare("SELECT medication_name, dosage, frequency, duration, instructions FROM prescriptions WHERE patient_id = ? AND (visit_id = ? OR visit_id IS NULL)");
            $rxStmt->execute([$patientId, $visitId]);
            $prescriptionsData = $rxStmt->fetchAll();

            // Doctor name
            $doctorName = $_SESSION['user_name'] ?? 'Dr. Sarah Jenkins';

            // Insert into past_visits with complete records saved as JSON
            $pvId = "pv-" . time();
            $pvInsert = $pdo->prepare("INSERT INTO past_visits (id, patient_id, visit_id, visit_date, title, summary, doctor_name, vitals, soap_notes, prescriptions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $pvInsert->execute([
                $pvId,
                $patientId,
                $visitId,
                date('M d, Y'),
                "Clinical Encounter ($icdCode)",
                !empty($plan) ? (substr($plan, 0, 120) . (strlen($plan) > 120 ? '...' : '')) : "Clinical encounter completed.",
                $doctorName,
                json_encode($vitalsData),
                json_encode($soapData),
                json_encode($prescriptionsData)
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
            header("Location: ../clinical_visit.php?patient_id=$patientId&visit_id=$visitId");
            exit;
        }
    }
}
