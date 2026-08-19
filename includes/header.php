<?php
/**
 * Common Header Layout Component with Mobile Responsiveness
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

$pdo = getDB();
$toast = getToast();
$activePage = $activePage ?? 'dashboard';

// Authentication Check
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    if (basename($_SERVER['PHP_SELF']) !== 'login.php' && basename($_SERVER['PHP_SELF']) !== 'install.php') {
        header("Location: login.php");
        exit;
    }
}

// Get doctors for current doctor selector
$doctors = $pdo->query("SELECT * FROM doctors ORDER BY name ASC")->fetchAll();
$currentDoctorId = $_SESSION['current_doctor_id'] ?? ($doctors[0]['id'] ?? 'doc-1');
$currentDoctor = array_filter($doctors, fn($d) => $d['id'] === $currentDoctorId);
$currentDoctor = reset($currentDoctor) ?: ($doctors[0] ?? ['id' => 'doc-1', 'name' => 'Dr. Sarah Jenkins', 'specialty' => 'Internal Medicine']);
?>
<!DOCTYPE html>
<html class="light h-full bg-[#f8f9ff]" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($pageTitle ?? 'ClinicFlow') ?></title>

    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@400;500&amp;display=swap" rel="stylesheet"/>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "surface-dim": "#cbdbf5",
              "on-tertiary-fixed": "#2a1700",
              "on-tertiary": "#ffffff",
              "on-background": "#0b1c30",
              "surface-container-low": "#eff4ff",
              "tertiary-fixed-dim": "#ffb95f",
              "surface-container": "#e5eeff",
              "tertiary-container": "#7c4d00",
              "on-primary": "#ffffff",
              "surface-container-lowest": "#ffffff",
              "on-secondary-fixed": "#002113",
              "inverse-on-surface": "#eaf1ff",
              "inverse-primary": "#b0c6ff",
              "secondary": "#006c49",
              "primary-fixed": "#d9e2ff",
              "surface-bright": "#f8f9ff",
              "surface-container-highest": "#d3e4fe",
              "on-surface-variant": "#434653",
              "surface-container-high": "#dce9ff",
              "on-surface": "#0b1c30",
              "surface-variant": "#d3e4fe",
              "primary-container": "#0f52ba",
              "on-error-container": "#93000a",
              "inverse-surface": "#213145",
              "error-container": "#ffdad6",
              "primary": "#003c90",
              "tertiary-fixed": "#ffddb8",
              "secondary-fixed-dim": "#4edea3",
              "outline-variant": "#c3c6d5",
              "outline": "#737784",
              "on-primary-fixed-variant": "#00419c",
              "surface-tint": "#1d59c1",
              "secondary-container": "#6cf8bb",
              "on-primary-container": "#bcceff",
              "background": "#f8f9ff",
              "on-tertiary-container": "#ffc278",
              "tertiary": "#5c3800",
              "on-error": "#ffffff",
              "secondary-fixed": "#6ffbbe",
              "on-secondary-fixed-variant": "#005236",
              "on-tertiary-fixed-variant": "#653e00",
              "on-secondary": "#ffffff",
              "primary-fixed-dim": "#b0c6ff",
              "on-secondary-container": "#00714d",
              "error": "#ba1a1a",
              "on-primary-fixed": "#001945"
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
              mono: ["JetBrains Mono", "monospace"]
            }
          }
        }
      };
    </script>
    <style>
      .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      }
      .material-symbols-outlined.fill {
        font-variation-settings: 'FILL' 1;
      }
    </style>
</head>
<body class="h-full font-sans text-on-surface bg-background flex flex-col min-h-screen">

<!-- Toast Notification -->
<?php if ($toast): ?>
<div id="toast-notification" class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-gray-800 bg-white rounded-xl shadow-lg border border-slate-200 max-w-sm" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-9 h-9 text-<?= $toast['type'] === 'error' ? 'red-600 bg-red-100' : 'emerald-600 bg-emerald-100' ?> rounded-lg">
        <span class="material-symbols-outlined"><?= $toast['type'] === 'error' ? 'error' : 'check_circle' ?></span>
    </div>
    <div class="ms-3 text-sm font-normal pr-4">
        <div class="font-semibold text-gray-900"><?= htmlspecialchars($toast['title']) ?></div>
        <div class="text-xs text-slate-600"><?= htmlspecialchars($toast['message']) ?></div>
    </div>
    <button type="button" onclick="document.getElementById('toast-notification').remove()" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8">
        <span class="material-symbols-outlined text-sm">close</span>
    </button>
</div>
<script>
  setTimeout(() => {
    const el = document.getElementById('toast-notification');
    if (el) el.remove();
  }, 4000);
</script>
<?php endif; ?>

<!-- Top Navigation Bar -->
<header class="bg-surface-container-lowest border-b border-outline-variant/30 sticky top-0 z-30 shadow-xs">
    <div class="flex items-center justify-between px-3 sm:px-6 py-2.5 sm:py-3">
        <!-- Mobile Menu Toggle & Logo -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <button type="button" onclick="toggleMobileSidebar()" aria-label="Toggle Navigation Menu" class="md:hidden p-2 text-primary hover:bg-primary/10 rounded-xl transition flex items-center justify-center border border-primary/20">
                <span class="material-symbols-outlined text-2xl font-bold">menu</span>
            </button>
            <a href="index.php" class="flex items-center gap-2">
                <img src="assets/images/NuvisMedcareX_logo.jpg" alt="Nuvis Medcare X" class="h-7 sm:h-10 object-contain max-w-[120px] sm:max-w-none rounded-md">
            </a>
        </div>

        <!-- Global Search (Desktop) -->
        <div class="relative w-80 lg:w-96 hidden md:block">
            <form action="patients.php" method="GET" class="relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                <input type="text" name="q" placeholder="Search patients by name, MRN..." class="w-full bg-surface-container-low pl-10 pr-4 py-2 rounded-xl text-xs border border-transparent focus:border-primary focus:bg-white focus:outline-none transition-all">
            </form>
        </div>

        <!-- Right Quick Actions & Doctor Switcher -->
        <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
            <a href="register_patient.php" class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition shadow-sm">
                <span class="material-symbols-outlined text-base">person_add</span>
                <span class="hidden lg:inline">Register Patient</span>
            </a>
            <a href="calendar.php?action=book" class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 bg-primary-container text-white text-xs font-semibold rounded-xl hover:bg-primary-container/90 transition shadow-sm">
                <span class="material-symbols-outlined text-base">calendar_add_on</span>
                <span class="hidden lg:inline">Book Appointment</span>
            </a>

            <!-- Doctor Selector & Logout -->
            <div class="flex items-center gap-1.5 sm:gap-3 pl-1.5 sm:pl-3 border-l border-outline-variant/40">
                <form action="actions/set_doctor.php" method="POST" class="flex items-center gap-1 sm:gap-2">
                    <div class="flex items-center gap-1 sm:gap-2">
                        <img src="<?= htmlspecialchars($currentDoctor['avatar'] ?? '') ?>" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover border border-primary/20" alt="Doctor">
                        <select name="doctor_id" onchange="this.form.submit()" class="bg-surface-container-low border border-outline-variant/50 text-[11px] sm:text-xs font-semibold text-on-surface rounded-lg px-1.5 py-1 sm:px-2 sm:py-1.5 focus:outline-none max-w-[90px] sm:max-w-none truncate">
                            <?php foreach ($doctors as $doc): ?>
                                <option value="<?= htmlspecialchars($doc['id']) ?>" <?= $doc['id'] === $currentDoctor['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($doc['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                <a href="actions/logout.php" title="Sign Out" class="p-1.5 text-slate-500 hover:text-red-600 rounded-lg hover:bg-slate-100 transition flex items-center">
                    <span class="material-symbols-outlined text-lg">logout</span>
                </a>
            </div>
        </div>
    </div>
</header>

<div class="flex flex-1 overflow-hidden relative">
<?php include __DIR__ . '/sidebar.php'; ?>
<main class="flex-1 overflow-y-auto p-3 sm:p-6 w-full">
