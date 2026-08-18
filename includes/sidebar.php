<?php
/**
 * Shared Navigation Sidebar Component with Mobile Drawer
 */
$activePage = $activePage ?? 'dashboard';

$navItems = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'url' => 'index.php'],
    ['id' => 'patients', 'label' => 'Patients', 'icon' => 'group', 'url' => 'patients.php'],
    ['id' => 'calendar', 'label' => 'Calendar', 'icon' => 'calendar_month', 'url' => 'calendar.php'],
    ['id' => 'clinical-visit', 'label' => 'Clinical Encounter', 'icon' => 'clinical_notes', 'url' => 'clinical_visit.php'],
    ['id' => 'billing', 'label' => 'Billing & Financials', 'icon' => 'payments', 'url' => 'billing.php'],
    ['id' => 'inventory', 'label' => 'Inventory', 'icon' => 'inventory_2', 'url' => 'inventory.php'],
    ['id' => 'register-patient', 'label' => 'Register Patient', 'icon' => 'person_add', 'url' => 'register_patient.php'],
    ['id' => 'admin', 'label' => 'Administrator', 'icon' => 'admin_panel_settings', 'url' => 'admin.php'],
];
?>

<!-- Desktop Sidebar -->
<aside class="w-64 bg-surface-container-lowest border-r border-outline-variant/30 flex flex-col justify-between p-4 shrink-0 hidden md:flex">
    <div class="space-y-1">
        <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-outline/70">
            Main Menu
        </div>
        <nav class="space-y-1">
            <?php foreach ($navItems as $item):
                $isActive = ($activePage === $item['id']);
            ?>
                <a href="<?= $item['url'] ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all <?= $isActive ? 'bg-surface-container-high text-primary font-bold shadow-2xs' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface' ?>">
                    <span class="material-symbols-outlined text-lg <?= $isActive ? 'fill text-primary' : '' ?>"><?= $item['icon'] ?></span>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Quick Help & Doctor Profile Badge -->
    <div class="space-y-3 pt-4 border-t border-outline-variant/30">
        <div class="p-3 bg-surface-container-low rounded-xl text-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="font-bold text-on-surface">Clinic Status</span>
                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Open
                </span>
            </div>
            <p class="text-[11px] text-outline">Queue capacity: Standard</p>
        </div>
    </div>
</aside>

<!-- Mobile Overlay Backdrop -->
<div id="mobileBackdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden md:hidden transition-opacity"></div>

<!-- Mobile Drawer Sidebar -->
<aside id="mobileSidebar" class="fixed top-0 left-0 bottom-0 w-72 bg-surface-container-lowest z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden flex flex-col justify-between p-5 shadow-2xl border-r border-outline-variant/30">
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-outline-variant/30 pb-3">
            <div class="flex items-center gap-2">
                <img src="assets/images/nuvis_medico_logo.png" alt="Nuvis Medico" class="h-8 object-contain">
            </div>
            <button onclick="toggleMobileSidebar()" class="p-1.5 text-outline hover:text-on-surface rounded-lg">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <nav class="space-y-1">
            <?php foreach ($navItems as $item):
                $isActive = ($activePage === $item['id']);
            ?>
                <a href="<?= $item['url'] ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs transition-all <?= $isActive ? 'bg-surface-container-high text-primary font-bold shadow-2xs' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface' ?>">
                    <span class="material-symbols-outlined text-lg <?= $isActive ? 'fill text-primary' : '' ?>"><?= $item['icon'] ?></span>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="space-y-3 pt-4 border-t border-outline-variant/30">
        <a href="register_patient.php" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white text-xs font-semibold rounded-xl shadow-xs">
            <span class="material-symbols-outlined text-base">person_add</span>
            <span>Register Patient</span>
        </a>
        <a href="calendar.php?action=book" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-container text-white text-xs font-semibold rounded-xl shadow-xs">
            <span class="material-symbols-outlined text-base">calendar_add_on</span>
            <span>Book Appointment</span>
        </a>
    </div>
</aside>

<script>
function toggleMobileSidebar() {
    const sidebar = document.getElementById('mobileSidebar');
    const backdrop = document.getElementById('mobileBackdrop');
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
    }
}
</script>
