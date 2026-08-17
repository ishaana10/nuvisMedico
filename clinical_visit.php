<?php
$patientId = $_GET['patient_id'] ?? 'pat-1';

require_once __DIR__ . '/config/database.php';
$pdo = getDB();

// Fetch patient info
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if (!$patient) {
    header("Location: patients.php");
    exit;
}

// Fetch vitals
$vitalsStmt = $pdo->prepare("SELECT * FROM vitals WHERE patient_id = ? ORDER BY updated_at DESC LIMIT 1");
$vitalsStmt->execute([$patientId]);
$vitals = $vitalsStmt->fetch() ?: [
    'blood_pressure' => '120/80',
    'heart_rate' => 72,
    'temperature' => 98.6,
    'weight' => 145,
    'height' => 66,
    'bmi' => 23.4,
    'oxygen_sat' => 99
];

// Fetch SOAP notes
$soapStmt = $pdo->prepare("SELECT * FROM soap_notes WHERE patient_id = ? ORDER BY updated_at DESC LIMIT 1");
$soapStmt->execute([$patientId]);
$soap = $soapStmt->fetch() ?: [
    'subjective' => 'Patient reports for clinical evaluation.',
    'objective' => 'Vitals stable. Alert and oriented x4.',
    'assessment_codes' => json_encode([['code' => 'J01.90', 'label' => 'Acute sinusitis, unspecified']]),
    'plan' => 'Advised rest and hydration. Follow up PRN.'
];

$assessmentCodes = json_decode($soap['assessment_codes'] ?? '[]', true) ?: [];

// Fetch Prescriptions
$rxStmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ? ORDER BY created_at ASC");
$rxStmt->execute([$patientId]);
$prescriptions = $rxStmt->fetchAll();

$pageTitle = "Clinical Encounter - " . htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']);
$activePage = "clinical-visit";
include __DIR__ . '/includes/header.php';
?>

<!-- Patient Encounter Header Bar -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 mb-6 shadow-xs">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-primary text-white font-bold text-lg flex items-center justify-center">
                <?= htmlspecialchars($patient['initials'] ?: 'P') ?>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold text-on-surface"><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></h1>
                    <span class="px-2 py-0.5 rounded bg-primary-fixed text-on-primary-fixed text-[11px] font-bold font-mono">
                        <?= htmlspecialchars($patient['mrn']) ?>
                    </span>
                </div>
                <p class="text-xs text-outline font-medium mt-0.5">
                    <?= htmlspecialchars($patient['age']) ?> yrs • <?= htmlspecialchars($patient['gender']) ?> • DOB: <?= htmlspecialchars($patient['dob']) ?> • Attending: <strong class="text-on-surface"><?= htmlspecialchars($currentDoctor['name']) ?></strong>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="print_prescription.php?patient_id=<?= htmlspecialchars($patient['id']) ?>" target="_blank" class="px-3.5 py-2 bg-surface-container-high text-primary font-semibold text-xs rounded-xl hover:bg-surface-container-highest transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">print</span>
                <span>Print Rx</span>
            </a>
            <form action="actions/encounter_save.php" method="POST">
                <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient['id']) ?>">
                <input type="hidden" name="action" value="finish">
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-semibold text-xs rounded-xl hover:bg-emerald-700 transition shadow-xs flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">task_alt</span>
                    <span>Finalize Visit</span>
                </button>
            </form>
        </div>
    </div>
</div>

<form action="actions/encounter_save.php" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient['id']) ?>">
    <input type="hidden" name="action" value="save">

    <!-- Left Column (2 Cols): Vitals & SOAP Notes -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Vitals Input Section -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <h2 class="text-xs font-bold text-outline uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">vital_signs</span>
                <span>Patient Vitals</span>
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Blood Pressure</label>
                    <input type="text" name="blood_pressure" value="<?= htmlspecialchars($vitals['blood_pressure']) ?>" placeholder="120/80" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-mono font-bold text-on-surface">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Heart Rate (bpm)</label>
                    <input type="number" name="heart_rate" value="<?= htmlspecialchars($vitals['heart_rate']) ?>" placeholder="72" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-mono font-bold text-on-surface">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Temperature (°F)</label>
                    <input type="text" name="temperature" value="<?= htmlspecialchars($vitals['temperature']) ?>" placeholder="98.6" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-mono font-bold text-on-surface">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">SpO2 (%)</label>
                    <input type="number" name="oxygen_sat" value="<?= htmlspecialchars($vitals['oxygen_sat']) ?>" placeholder="99" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-mono font-bold text-on-surface">
                </div>
            </div>
        </div>

        <!-- SOAP Notes Form -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs space-y-4">
            <h2 class="text-xs font-bold text-outline uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">edit_note</span>
                <span>SOAP Encounter Documentation</span>
            </h2>

            <div class="text-xs space-y-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Subjective (Chief Complaint & History)</label>
                    <textarea name="subjective" rows="3" class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium"><?= htmlspecialchars($soap['subjective']) ?></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Objective (Physical Exam & Diagnostic Data)</label>
                    <textarea name="objective" rows="3" class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium"><?= htmlspecialchars($soap['objective']) ?></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Assessment & ICD-10 Code</label>
                    <input type="text" name="icd_code" value="<?= htmlspecialchars($assessmentCodes[0]['code'] ?? 'J01.90') ?>" placeholder="e.g. J01.90 - Acute sinusitis" class="w-full bg-surface-container-low px-3.5 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Plan (Treatment & Follow-up)</label>
                    <textarea name="plan" rows="3" class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium"><?= htmlspecialchars($soap['plan']) ?></textarea>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-5 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition shadow-xs">
                    Save Vitals & SOAP Notes
                </button>
            </div>
        </div>
    </div>

    <!-- Right Column (1 Col): Prescriptions Section -->
    <div class="space-y-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <h2 class="text-xs font-bold text-outline uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">prescriptions</span>
                <span>Active Prescriptions</span>
            </h2>

            <!-- Existing Prescriptions -->
            <div class="space-y-3 mb-4">
                <?php if (empty($prescriptions)): ?>
                    <p class="text-xs text-outline italic">No medications added yet.</p>
                <?php endif; ?>
                <?php foreach ($prescriptions as $rx): ?>
                    <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/30 flex items-start justify-between">
                        <div>
                            <p class="font-bold text-xs text-on-surface"><?= htmlspecialchars($rx['medication_name']) ?> <span class="text-primary font-mono"><?= htmlspecialchars($rx['dosage']) ?></span></p>
                            <p class="text-[11px] text-outline mt-0.5"><?= htmlspecialchars($rx['frequency']) ?> • <?= htmlspecialchars($rx['duration']) ?></p>
                            <?php if (!empty($rx['instructions'])): ?>
                                <p class="text-[10px] text-slate-600 mt-1 italic"><?= htmlspecialchars($rx['instructions']) ?></p>
                            <?php endif; ?>
                        </div>
                        <a href="actions/encounter_save.php?action=delete_rx&rx_id=<?= htmlspecialchars($rx['id']) ?>&patient_id=<?= htmlspecialchars($patient['id']) ?>" class="text-red-500 hover:text-red-700">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Add Prescription Form -->
            <div class="pt-4 border-t border-outline-variant/20 space-y-3 text-xs">
                <p class="font-bold text-slate-700">Add New Medication Line</p>
                <div>
                    <input type="text" name="medication_name" placeholder="Medication Name (e.g. Amoxicillin)" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="dosage" placeholder="Dosage (500mg)" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                    <input type="text" name="frequency" placeholder="Freq (BID)" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>
                <div>
                    <input type="text" name="duration" placeholder="Duration (7 days)" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>
                <div>
                    <input type="text" name="instructions" placeholder="Instructions (Take with food)" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>
                <button type="submit" name="add_rx" value="1" class="w-full py-2 bg-primary-container text-white text-xs font-semibold rounded-xl hover:bg-primary-container/90 transition shadow-xs">
                    + Add Medication Line
                </button>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
