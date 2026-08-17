<?php
/**
 * Shared Navigation Sidebar Component
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
