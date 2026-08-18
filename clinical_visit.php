<?php
$patientId = $_GET['patient_id'] ?? 'pat-1';
$visitId = $_GET['visit_id'] ?? ''; // Present if editing an existing encounter
$mode = $_GET['mode'] ?? ($visitId ? 'edit' : 'new');

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

// Fetch Prescriptions based on mode (New vs Edit Encounter)
$prescriptions = [];
if ($mode === 'edit' || !empty($visitId)) {
    $rxStmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ? ORDER BY created_at ASC");
    $rxStmt->execute([$patientId]);
    $prescriptions = $rxStmt->fetchAll();
}

// Fetch Historical Prescriptions for Patient History tab
$historicalRxStmt = $pdo->prepare("SELECT * FROM prescriptions WHERE patient_id = ? ORDER BY created_at DESC");
$historicalRxStmt->execute([$patientId]);
$historicalPrescriptions = $historicalRxStmt->fetchAll();

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

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" onclick="document.getElementById('issueMedCertModal').classList.remove('hidden')" class="px-3.5 py-2 bg-emerald-700 text-white font-semibold text-xs rounded-xl hover:bg-emerald-800 transition shadow-xs flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">workspace_premium</span>
                <span>Medical Cert</span>
            </button>
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

    <!-- Right Column (1 Col): Prescriptions Section & History Tab -->
    <div class="space-y-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-2">
                <h2 class="text-xs font-bold text-outline uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">prescriptions</span>
                    <span>Encounter Prescription</span>
                </h2>
                <?php if ($mode === 'new'): ?>
                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-[10px] font-bold">New Encounter (Blank Rx)</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold">Editing Encounter</span>
                <?php endif; ?>
            </div>

            <!-- Current Encounter Prescriptions -->
            <div class="space-y-3 mb-4">
                <?php if (empty($prescriptions)): ?>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic">
                        Prescription form is blank for this new encounter. Add new medication lines below.
                    </div>
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

        <!-- Prescription History Tab (Previous Prescriptions) -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <h2 class="text-xs font-bold text-outline uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-base">history</span>
                <span>Prescription History</span>
            </h2>

            <?php if (empty($historicalPrescriptions)): ?>
                <p class="text-xs text-outline italic">No past prescription history found.</p>
            <?php else: ?>
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    <?php foreach ($historicalPrescriptions as $hrx): ?>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($hrx['medication_name']) ?> (<?= htmlspecialchars($hrx['dosage']) ?>)</span>
                                <span class="text-[10px] text-slate-400 font-mono"><?= date('M d, Y', strtotime($hrx['created_at'])) ?></span>
                            </div>
                            <p class="text-[11px] text-slate-600"><?= htmlspecialchars($hrx['frequency']) ?> • <?= htmlspecialchars($hrx['duration']) ?></p>
                            <?php if (!empty($hrx['instructions'])): ?>
                                <p class="text-[10px] text-slate-500 italic"><?= htmlspecialchars($hrx['instructions']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<!-- Modal: Issue Medical Certificate in Clinical Visit -->
<?php
$settingsRows = $pdo->query("SELECT * FROM clinic_settings")->fetchAll();
$clinicSettings = [];
foreach ($settingsRows as $sr) {
    $clinicSettings[$sr['setting_key']] = $sr['setting_value'];
}
?>
<div id="issueMedCertModal" class="fixed inset-0 bg-black/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-outline-variant/30 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-outline-variant/20">
            <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600">workspace_premium</span>
                <span>Issue Medical Certificate</span>
            </h3>
            <button type="button" onclick="document.getElementById('issueMedCertModal').classList.add('hidden')" class="text-outline hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="actions/medical_certificate_save.php" method="POST" class="space-y-4 text-xs">
            <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient['id']) ?>">
            <input type="hidden" name="print_immediately" value="1">

            <div>
                <label class="block font-bold text-slate-700 mb-1">Issue Date</label>
                <input type="date" name="issue_date" value="<?= date('Y-m-d') ?>" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium" required>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Diagnosis / Clinical Impression <span class="text-red-500">*</span></label>
                <textarea name="diagnosis" rows="2" placeholder="e.g. Acute Upper Respiratory Tract Infection" class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 font-medium" required><?= htmlspecialchars($soap['subjective'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Fitness Status / Classification</label>
                <select name="fitness_status" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-semibold">
                    <option value="Fit for Work / School">Fit for Work / School</option>
                    <option value="Fit to Resume Normal Duties">Fit to Resume Normal Duties</option>
                    <option value="Unfit for Physical Activity">Unfit for Physical Activity</option>
                    <option value="Needs Medical Rest">Needs Medical Rest</option>
                    <option value="Fit with Restrictions">Fit with Restrictions</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Rest Duration / Fit Details</label>
                <input type="text" name="fit_status_details" placeholder="e.g. Advised medical leave for 3 days" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Remarks & Recommendations</label>
                <textarea name="recommendations" rows="2" placeholder="e.g. Continuous rest, medications as prescribed." class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 font-medium"><?= htmlspecialchars($soap['plan'] ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2 border-t border-outline-variant/20">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Attending Physician</label>
                    <input type="text" name="doctor_name" value="<?= htmlspecialchars($_SESSION['user_name'] ?? $currentDoctor['name'] ?? 'Dr. Sarah Jenkins') ?>" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium" required>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">PRC License No.</label>
                    <input type="text" name="prc_number" value="<?= htmlspecialchars($clinicSettings['doc_prc_no'] ?? 'PRC-0098412') ?>" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-mono font-medium">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">PTR License No.</label>
                    <input type="text" name="ptr_number" value="<?= htmlspecialchars($clinicSettings['doc_ptr_no'] ?? 'PTR-8842109') ?>" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-mono font-medium">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant/20">
                <button type="button" onclick="document.getElementById('issueMedCertModal').classList.add('hidden')" class="px-4 py-2 bg-surface-container-high text-on-surface font-semibold rounded-xl hover:bg-surface-variant transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition shadow-xs flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">print</span>
                    <span>Issue & Print Certificate</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
