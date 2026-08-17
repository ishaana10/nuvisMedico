<?php
$patientId = $_GET['id'] ?? 'pat-1';

require_once __DIR__ . '/config/database.php';
$pdo = getDB();

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if (!$patient) {
    header("Location: patients.php");
    exit;
}

$pageTitle = htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) . " - Patient Chart";
$activePage = "patients";
include __DIR__ . '/includes/header.php';

// Fetch past visits
$pvStmt = $pdo->prepare("SELECT * FROM past_visits WHERE patient_id = ? ORDER BY id DESC");
$pvStmt->execute([$patientId]);
$pastVisits = $pvStmt->fetchAll();

// Fetch appointments
$aptStmt = $pdo->prepare("SELECT * FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC");
$aptStmt->execute([$patientId]);
$patientAppts = $aptStmt->fetchAll();
?>

<!-- Patient Header Card -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-6 mb-6 shadow-xs">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <?php if (!empty($patient['avatar'])): ?>
                <img src="<?= htmlspecialchars($patient['avatar']) ?>" class="w-16 h-16 rounded-2xl object-cover border-2 border-primary/20 shadow-xs" alt="Avatar">
            <?php else: ?>
                <div class="w-16 h-16 rounded-2xl bg-primary text-white font-bold text-2xl flex items-center justify-center shadow-xs">
                    <?= htmlspecialchars($patient['initials'] ?: 'P') ?>
                </div>
            <?php endif; ?>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-on-surface"><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed text-xs font-bold font-mono">
                        <?= htmlspecialchars($patient['mrn']) ?>
                    </span>
                </div>
                <p class="text-xs text-outline mt-1 font-medium">
                    <?= htmlspecialchars($patient['age']) ?> Yrs • <?= htmlspecialchars($patient['gender']) ?> • DOB: <?= htmlspecialchars($patient['dob']) ?> • Blood: <?= htmlspecialchars($patient['blood_group'] ?: 'O+') ?>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="clinical_visit.php?patient_id=<?= htmlspecialchars($patient['id']) ?>" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition shadow-xs flex items-center gap-2">
                <span class="material-symbols-outlined text-base">clinical_notes</span>
                <span>Start Encounter</span>
            </a>
            <a href="calendar.php?action=book&patient_id=<?= htmlspecialchars($patient['id']) ?>" class="px-4 py-2 bg-primary-container text-white text-xs font-semibold rounded-xl hover:bg-primary-container/90 transition shadow-xs flex items-center gap-2">
                <span class="material-symbols-outlined text-base">calendar_add_on</span>
                <span>Schedule Visit</span>
            </a>
        </div>
    </div>
</div>

<!-- Grid Layout: Overview & History -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Patient Details & Medical Alert -->
    <div class="space-y-6">
        <!-- Medical Overview -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <h2 class="text-sm font-bold text-on-surface mb-3 uppercase tracking-wider text-outline">Clinical Overview</h2>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="font-bold text-slate-500 block mb-1">Known Allergies</label>
                    <?php if (!empty($patient['known_allergies']) && $patient['known_allergies'] !== 'None'): ?>
                        <div class="p-2.5 rounded-xl bg-amber-50 text-amber-900 border border-amber-200 font-medium flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-600 text-base">warning</span>
                            <span><?= htmlspecialchars($patient['known_allergies']) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="text-slate-600">No known drug allergies (NKDA)</div>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="font-bold text-slate-500 block mb-1">Chronic Conditions</label>
                    <div class="p-2.5 rounded-xl bg-surface-container-low font-medium text-on-surface">
                        <?= htmlspecialchars($patient['chronic_conditions'] ?: 'None documented') ?>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-500 block mb-1">Clinical Notes</label>
                    <div class="p-2.5 rounded-xl bg-surface-container-low font-medium text-on-surface-variant">
                        <?= htmlspecialchars($patient['clinical_notes'] ?: 'No additional notes.') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insurance & Contact Info -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <h2 class="text-sm font-bold text-on-surface mb-3 uppercase tracking-wider text-outline">Contact & Insurance</h2>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-500 font-semibold block">Phone</span>
                    <span class="font-bold text-on-surface"><?= htmlspecialchars($patient['phone']) ?></span>
                </div>
                <div>
                    <span class="text-slate-500 font-semibold block">Email</span>
                    <span class="font-medium text-on-surface"><?= htmlspecialchars($patient['email']) ?></span>
                </div>
                <div>
                    <span class="text-slate-500 font-semibold block">Address</span>
                    <span class="font-medium text-on-surface"><?= htmlspecialchars($patient['address']) ?></span>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-500 font-semibold block">Insurance Provider</span>
                    <span class="font-bold text-primary"><?= htmlspecialchars($patient['insurance_provider'] ?: 'Self Pay') ?></span>
                    <p class="text-[11px] text-slate-500 font-mono">Policy: <?= htmlspecialchars($patient['insurance_policy_number'] ?: 'N/A') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Past Encounters & Timeline -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <h2 class="text-base font-bold text-on-surface mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">history_edu</span>
                <span>Past Clinical Visits & Encounters</span>
            </h2>

            <?php if (empty($pastVisits)): ?>
                <div class="p-6 text-center text-outline text-xs bg-surface-container-low/50 rounded-xl">
                    No past visits archived for this patient yet.
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($pastVisits as $pv): ?>
                        <div class="p-4 rounded-xl bg-surface-container-low/40 border border-outline-variant/20">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-bold text-xs text-on-surface"><?= htmlspecialchars($pv['title']) ?></h3>
                                <span class="text-[11px] text-outline font-medium"><?= htmlspecialchars($pv['visit_date']) ?></span>
                            </div>
                            <p class="text-xs text-on-surface-variant mb-2"><?= htmlspecialchars($pv['summary']) ?></p>
                            <div class="flex items-center justify-between text-[11px] text-outline pt-2 border-t border-outline-variant/20">
                                <span>Attending: <strong class="text-slate-700"><?= htmlspecialchars($pv['doctor_name']) ?></strong></span>
                                <span class="text-primary font-semibold">Archived File</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
