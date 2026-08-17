<?php
$patientId = $_GET['patient_id'] ?? 'pat-1';

require_once __DIR__ . '/config/database.php';
$pdo = getDB();

// Fetch Clinic Settings for custom prescription customization
$settingsRows = $pdo->query("SELECT * FROM clinic_settings")->fetchAll();
$settings = [];
foreach ($settingsRows as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}

$clinicName = $settings['clinic_name'] ?? 'ClinicFlow Medical Center';
$clinicSubtitle = $settings['clinic_subtitle'] ?? 'Integrated Primary & Specialist Healthcare';
$clinicAddress = $settings['clinic_address'] ?? '100 Healthcare Way, Suite 400, Springfield, OR 97477';
$clinicPhone = $settings['clinic_phone'] ?? '(555) 019-2831';
$clinicEmail = $settings['clinic_email'] ?? 'contact@clinicflow.com';
$clinicDea = $settings['clinic_dea'] ?? 'FC9823019';
$clinicNpi = $settings['clinic_npi'] ?? '1092830192';

$rxHeaderTitle = $settings['rx_header_title'] ?? 'OFFICIAL MEDICAL PRESCRIPTION';
$rxDisclaimer = $settings['rx_disclaimer'] ?? 'Notice: This prescription is valid for 30 days from date of issue unless specified otherwise.';
$rxFooterNote = $settings['rx_footer_note'] ?? 'Substitution Permitted unless DAW (Dispense As Written) is indicated.';

// Fetch patient info
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if (!$patient) {
    die("Patient file not found.");
}

$rxStmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ? ORDER BY created_at ASC");
$rxStmt->execute([$patientId]);
$prescriptions = $rxStmt->fetchAll();

$soapStmt = $pdo->prepare("SELECT * FROM soap_notes WHERE patient_id = ? ORDER BY updated_at DESC LIMIT 1");
$soapStmt->execute([$patientId]);
$soap = $soapStmt->fetch();
$assessmentCodes = json_decode($soap['assessment_codes'] ?? '[]', true) ?: [];

// Get attending doctor details
$docId = $_SESSION['current_doctor_id'] ?? 'doc-1';
$docStmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$docStmt->execute([$docId]);
$attendingDoc = $docStmt->fetch() ?: ['name' => 'Dr. Sarah Jenkins', 'specialty' => 'Internal Medicine'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 min-h-screen text-slate-800 font-sans">

<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
    <!-- Action buttons -->
    <div class="no-print mb-6 flex justify-between items-center bg-slate-50 p-4 rounded-xl border border-slate-200">
        <a href="clinical_visit.php?patient_id=<?= htmlspecialchars($patient['id']) ?>" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to Encounter</a>
        <div class="flex gap-2">
            <a href="admin.php" class="px-3 py-2 bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-300 transition">Customize Template</a>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">Print Official Prescription</button>
        </div>
    </div>

    <!-- Customized Clinic Header -->
    <div class="border-b-2 border-blue-900 pb-4 mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-blue-900 uppercase tracking-tight"><?= htmlspecialchars($clinicName) ?></h1>
            <p class="text-xs text-blue-700 font-medium"><?= htmlspecialchars($clinicSubtitle) ?></p>
            <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($clinicAddress) ?> • Phone: <?= htmlspecialchars($clinicPhone) ?></p>
            <p class="text-xs text-slate-500">DEA: <?= htmlspecialchars($clinicDea) ?> • NPI: <?= htmlspecialchars($clinicNpi) ?></p>
        </div>
        <div class="text-right">
            <span class="text-2xl font-serif font-bold text-blue-900">Rx</span>
            <p class="text-xs text-slate-500 font-mono mt-1">Date: <?= date('M d, Y') ?></p>
        </div>
    </div>

    <!-- Header Prescription Title -->
    <div class="text-center mb-6">
        <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500 border-y border-slate-200 py-1.5"><?= htmlspecialchars($rxHeaderTitle) ?></h2>
    </div>

    <!-- Patient Header -->
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6 text-xs grid grid-cols-2 gap-4">
        <div>
            <p class="text-slate-500 font-semibold uppercase text-[10px]">Patient Name</p>
            <p class="font-bold text-sm text-slate-900"><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
            <p class="text-slate-500 mt-1">DOB: <?= htmlspecialchars($patient['dob']) ?> (<?= htmlspecialchars($patient['age']) ?> Yrs)</p>
        </div>
        <div>
            <p class="text-slate-500 font-semibold uppercase text-[10px]">Medical Record No.</p>
            <p class="font-bold text-sm font-mono text-slate-900"><?= htmlspecialchars($patient['mrn']) ?></p>
            <p class="text-slate-500 mt-1">Allergies: <strong class="text-red-600"><?= htmlspecialchars($patient['known_allergies'] ?: 'NKDA') ?></strong></p>
        </div>
    </div>

    <!-- Diagnosis / Assessment -->
    <?php if (!empty($assessmentCodes)): ?>
    <div class="mb-6 text-xs">
        <p class="text-slate-500 font-semibold uppercase text-[10px] mb-1">ICD-10 Diagnosis</p>
        <p class="font-medium bg-blue-50 text-blue-900 p-2 rounded-lg inline-block border border-blue-100">
            <?= htmlspecialchars($assessmentCodes[0]['code'] ?? '') ?> - <?= htmlspecialchars($assessmentCodes[0]['label'] ?? '') ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Prescription Lines -->
    <div class="mb-8">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2 mb-4">Prescribed Medication Details</h2>
        <div class="space-y-4">
            <?php foreach ($prescriptions as $index => $rx): ?>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex justify-between items-baseline">
                        <span class="font-bold text-base text-slate-900"><?= ($index + 1) ?>. <?= htmlspecialchars($rx['medication_name']) ?></span>
                        <span class="font-mono font-bold text-blue-800 text-sm"><?= htmlspecialchars($rx['dosage']) ?></span>
                    </div>
                    <div class="mt-2 text-xs text-slate-700 flex gap-6">
                        <span><strong>Frequency:</strong> <?= htmlspecialchars($rx['frequency']) ?></span>
                        <span><strong>Duration:</strong> <?= htmlspecialchars($rx['duration']) ?></span>
                    </div>
                    <?php if (!empty($rx['instructions'])): ?>
                        <p class="mt-2 text-xs text-slate-600 bg-white p-2 rounded border border-slate-100">
                            <strong>Sig / Instructions:</strong> <?= htmlspecialchars($rx['instructions']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Custom Disclaimer & Refill Notes -->
    <div class="mb-8 p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
        <p><strong>Refill Policy:</strong> <?= htmlspecialchars($rxFooterNote) ?></p>
        <p class="italic text-slate-500"><?= htmlspecialchars($rxDisclaimer) ?></p>
    </div>

    <!-- Physician Signature Block -->
    <div class="pt-6 border-t border-slate-300 flex justify-between items-end text-xs">
        <div>
            <p class="text-slate-500 text-[10px] uppercase font-semibold">Substitution</p>
            <p class="font-medium text-slate-700">Refill: [ ] 0  [ ] 1  [ ] 2  [ ] 3  [ ] PRN</p>
        </div>
        <div class="text-right w-64">
            <div class="border-b border-slate-400 mb-1 pb-2 font-serif text-lg font-bold text-blue-900 italic"><?= htmlspecialchars($attendingDoc['name']) ?></div>
            <p class="font-bold text-slate-800"><?= htmlspecialchars($attendingDoc['name']) ?></p>
            <p class="text-slate-500"><?= htmlspecialchars($attendingDoc['specialty']) ?></p>
        </div>
    </div>
</div>

</body>
</html>
