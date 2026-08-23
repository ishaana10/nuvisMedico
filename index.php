<?php
// Serve dist static assets if requested (handles relative path requests under subdirectories)
$uri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
if (str_contains($uri, '/assets/')) {
    $assetPos = strpos($uri, '/assets/');
    $assetRel = substr($uri, $assetPos);
    $assetFile = __DIR__ . '/dist' . $assetRel;
    if (file_exists($assetFile) && is_file($assetFile)) {
        $mime = str_ends_with($assetFile, '.css') ? 'text/css' : (str_ends_with($assetFile, '.js') ? 'application/javascript' : 'text/plain');
        header("Content-Type: $mime");
        readfile($assetFile);
        exit;
    }
}

// Serve React single-page app if dist/index.html exists and classic view is not explicitly requested
$viewMode = $_GET['view'] ?? 'react';
if ($viewMode === 'react' && file_exists(__DIR__ . '/dist/index.html')) {
    header('Content-Type: text/html; charset=UTF-8');
    readfile(__DIR__ . '/dist/index.html');
    exit;
}

$pageTitle = "Dashboard - ClinicFlow";
$activePage = "dashboard";
include __DIR__ . '/includes/header.php';

// Fetch key dashboard statistics
$todayDate = date('Y-m-d');

// 1. Queue Items
$queueStmt = $pdo->query("SELECT * FROM queue ORDER BY id ASC");
$queueItems = $queueStmt->fetchAll();

// 2. Metrics
$waitingCount = count(array_filter($queueItems, fn($q) => $q['status'] === 'Waiting'));
$inRoomCount = count(array_filter($queueItems, fn($q) => $q['status'] === 'In Room'));

$todayApptsStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ? OR appointment_date = '2023-10-24'");
$todayApptsStmt->execute([$todayDate]);
$todayApptsCount = $todayApptsStmt->fetchColumn();
$totalPatientsCount = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();

// 3. Today's Scheduled Appointments
$apptsStmt = $pdo->query("SELECT * FROM appointments ORDER BY time ASC LIMIT 6");
$appointments = $apptsStmt->fetchAll();

// 4. Recent Activities
$activities = $pdo->query("SELECT * FROM activities ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!-- Dashboard Top Banner -->
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-on-surface">Clinic Overview</h1>
        <p class="text-xs text-outline font-medium">Welcome back, <?= htmlspecialchars($currentDoctor['name']) ?>. Here is today's schedule and live queue.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="register_patient.php" class="px-4 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition shadow-xs flex items-center gap-2">
            <span class="material-symbols-outlined text-base">person_add</span>
            <span>New Patient</span>
        </a>
        <a href="calendar.php?action=book" class="px-4 py-2 bg-primary-container text-white text-xs font-semibold rounded-xl hover:bg-primary-container/90 transition shadow-xs flex items-center gap-2">
            <span class="material-symbols-outlined text-base">calendar_add_on</span>
            <span>Book Appointment</span>
        </a>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 flex items-center justify-between shadow-xs">
        <div>
            <p class="text-[11px] font-bold text-outline uppercase tracking-wider">Patients in Queue</p>
            <h3 class="text-2xl font-bold text-on-surface mt-1"><?= count($queueItems) ?></h3>
            <p class="text-[11px] text-amber-600 font-semibold mt-1"><?= $waitingCount ?> Waiting • <?= $inRoomCount ?> In Room</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">hourglass_top</span>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 flex items-center justify-between shadow-xs">
        <div>
            <p class="text-[11px] font-bold text-outline uppercase tracking-wider">Today's Appointments</p>
            <h3 class="text-2xl font-bold text-on-surface mt-1"><?= $todayApptsCount ?></h3>
            <p class="text-[11px] text-emerald-600 font-semibold mt-1">Scheduled for today</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">calendar_today</span>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 flex items-center justify-between shadow-xs">
        <div>
            <p class="text-[11px] font-bold text-outline uppercase tracking-wider">Total Registered</p>
            <h3 class="text-2xl font-bold text-on-surface mt-1"><?= $totalPatientsCount ?></h3>
            <p class="text-[11px] text-primary font-semibold mt-1">Active Patient Files</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">group</span>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 flex items-center justify-between shadow-xs">
        <div>
            <p class="text-[11px] font-bold text-outline uppercase tracking-wider">Low Stock Alert</p>
            <h3 class="text-2xl font-bold text-on-surface mt-1">3</h3>
            <p class="text-[11px] text-red-600 font-semibold mt-1">Items need restocking</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">inventory_2</span>
        </div>
    </div>
</div>

<!-- Main Section: Live Patient Queue & Schedule -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Live Patient Queue (2 Cols) -->
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">groups</span>
                    <span>Live Patient Queue</span>
                </h2>
                <p class="text-xs text-outline">Manage active arrivals, check-ins, and room assignments</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                <?= count($queueItems) ?> Active
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-outline-variant/30 text-outline uppercase text-[10px] font-bold tracking-wider">
                        <th class="py-2.5 px-3">Patient</th>
                        <th class="py-2.5 px-3">MRN</th>
                        <th class="py-2.5 px-3">Time</th>
                        <th class="py-2.5 px-3">Doctor</th>
                        <th class="py-2.5 px-3">Status / Room</th>
                        <th class="py-2.5 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <?php if (empty($queueItems)): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-outline text-xs">No patients currently in queue.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($queueItems as $q): ?>
                        <tr class="hover:bg-surface-container-low transition">
                            <td class="py-3 px-3 font-bold text-on-surface">
                                <a href="clinical_visit.php?patient_id=<?= htmlspecialchars($q['patient_id']) ?>" class="hover:underline text-primary">
                                    <?= htmlspecialchars($q['patient_name']) ?>
                                </a>
                            </td>
                            <td class="py-3 px-3 font-mono font-medium text-slate-600"><?= htmlspecialchars($q['mrn']) ?></td>
                            <td class="py-3 px-3 text-outline font-medium"><?= htmlspecialchars($q['time']) ?></td>
                            <td class="py-3 px-3 font-medium text-on-surface-variant"><?= htmlspecialchars($q['doctor_name']) ?></td>
                            <td class="py-3 px-3">
                                <?php if ($q['status'] === 'In Room'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                        <?= htmlspecialchars($q['room'] ?: 'In Room') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                        Waiting
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-3 text-right space-x-1">
                                <?php if ($q['status'] === 'Waiting'): ?>
                                    <form action="actions/queue_update.php" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                                        <input type="hidden" name="queue_id" value="<?= htmlspecialchars($q['id']) ?>">
                                        <input type="hidden" name="action" value="check_in">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-[11px] font-semibold hover:bg-emerald-700 transition">
                                            Check In
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="clinical_visit.php?patient_id=<?= htmlspecialchars($q['patient_id']) ?>" class="inline-block px-2.5 py-1 bg-primary text-white rounded-lg text-[11px] font-semibold hover:bg-primary/90 transition">
                                        Start Encounter
                                    </a>
                                <?php endif; ?>
                                <form action="actions/queue_update.php" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= getCsrfToken() ?>">
                                    <input type="hidden" name="queue_id" value="<?= htmlspecialchars($q['id']) ?>">
                                    <input type="hidden" name="action" value="complete">
                                    <button type="submit" class="px-2.5 py-1 bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold hover:bg-slate-300 transition">
                                        Complete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity Log (1 Col) -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
        <h2 class="text-base font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">history</span>
            <span>Recent Activity</span>
        </h2>
        <div class="space-y-4">
            <?php foreach ($activities as $act): ?>
                <div class="flex items-start gap-3 text-xs">
                    <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                    <div>
                        <p class="font-semibold text-on-surface"><?= htmlspecialchars($act['title']) ?></p>
                        <p class="text-[11px] text-outline mt-0.5"><?= htmlspecialchars($act['detail']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
