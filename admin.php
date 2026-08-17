<?php
$pageTitle = "Administrator Settings - ClinicFlow";
$activePage = "admin";
include __DIR__ . '/includes/header.php';

// Fetch current clinic settings
$settingsRows = $pdo->query("SELECT * FROM clinic_settings")->fetchAll();
$settings = [];
foreach ($settingsRows as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}

// Fetch doctor staff
$doctorsList = $pdo->query("SELECT * FROM doctors ORDER BY name ASC")->fetchAll();
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-on-surface">System Administration</h1>
        <p class="text-xs text-outline font-medium">Manage clinic profile, staff accounts, and prescription templates</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left 2 Cols: Clinic & Prescription Customization Forms -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Clinic Branding & General Details -->
        <form action="actions/admin_save_settings.php" method="POST" class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-6 shadow-xs space-y-5">
            <h2 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-base">domain</span>
                <span>1. Clinic Branding & General Info</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Clinic Name <span class="text-red-500">*</span></label>
                    <input type="text" name="clinic_name" value="<?= htmlspecialchars($settings['clinic_name'] ?? 'ClinicFlow Medical Center') ?>" required class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-bold text-on-surface">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Subtitle / Slogan</label>
                    <input type="text" name="clinic_subtitle" value="<?= htmlspecialchars($settings['clinic_subtitle'] ?? 'Integrated Healthcare Center') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="clinic_phone" value="<?= htmlspecialchars($settings['clinic_phone'] ?? '(555) 019-2831') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Clinic Email</label>
                    <input type="email" name="clinic_email" value="<?= htmlspecialchars($settings['clinic_email'] ?? 'contact@clinicflow.com') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">DEA License Number</label>
                    <input type="text" name="clinic_dea" value="<?= htmlspecialchars($settings['clinic_dea'] ?? 'FC9823019') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-mono font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">NPI Number</label>
                    <input type="text" name="clinic_npi" value="<?= htmlspecialchars($settings['clinic_npi'] ?? '1092830192') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-mono font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Default Physician PRC License No.</label>
                    <input type="text" name="doc_prc_no" value="<?= htmlspecialchars($settings['doc_prc_no'] ?? 'PRC-0098412') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-mono font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Default Physician PTR No.</label>
                    <input type="text" name="doc_ptr_no" value="<?= htmlspecialchars($settings['doc_ptr_no'] ?? 'PTR-8842109') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-mono font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Physical Address</label>
                    <input type="text" name="clinic_address" value="<?= htmlspecialchars($settings['clinic_address'] ?? '100 Healthcare Way, Suite 400, Springfield, OR') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-medium">
                </div>
            </div>

            <hr class="border-outline-variant/20">

            <!-- Fully Customisable Prescription Settings -->
            <h2 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-base">print</span>
                <span>2. Prescription Template Customization</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Prescription Header Title</label>
                    <input type="text" name="rx_header_title" value="<?= htmlspecialchars($settings['rx_header_title'] ?? 'OFFICIAL MEDICAL PRESCRIPTION') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-bold">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Custom Disclaimer Text</label>
                    <textarea name="rx_disclaimer" rows="2" class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 font-medium"><?= htmlspecialchars($settings['rx_disclaimer'] ?? 'Notice: This prescription is valid for 30 days from date of issue.') ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Footer Instructions / Refill Policy Note</label>
                    <textarea name="rx_footer_note" rows="2" class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 font-medium"><?= htmlspecialchars($settings['rx_footer_note'] ?? 'Substitution Permitted unless DAW is indicated.') ?></textarea>
                </div>
            </div>

            <hr class="border-outline-variant/20">

            <!-- Fully Customisable Invoice & Receipt Settings -->
            <h2 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-base">receipt_long</span>
                <span>3. Invoice & Payment Receipt Customization</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Invoice Header Title</label>
                    <input type="text" name="invoice_header_title" value="<?= htmlspecialchars($settings['invoice_header_title'] ?? 'MEDICAL SERVICES INVOICE') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-bold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tax ID / EIN Number</label>
                    <input type="text" name="invoice_tax_id" value="<?= htmlspecialchars($settings['invoice_tax_id'] ?? '93-1029384') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-mono font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Invoice Payment Terms & Instructions</label>
                    <input type="text" name="invoice_payment_terms" value="<?= htmlspecialchars($settings['invoice_payment_terms'] ?? 'Net 30 Days. Please remit payment promptly.') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Invoice Footer Note</label>
                    <textarea name="invoice_footer_note" rows="2" class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 font-medium"><?= htmlspecialchars($settings['invoice_footer_note'] ?? 'Thank you for choosing ClinicFlow Medical Center for your care.') ?></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Receipt Header Title</label>
                    <input type="text" name="receipt_header_title" value="<?= htmlspecialchars($settings['receipt_header_title'] ?? 'OFFICIAL PAYMENT RECEIPT') ?>" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 font-bold">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Receipt Thank You & Confirmation Message</label>
                    <textarea name="receipt_thank_you_msg" rows="2" class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 font-medium"><?= htmlspecialchars($settings['receipt_thank_you_msg'] ?? 'Thank you for your payment. Your account balance for this invoice is cleared.') ?></textarea>
                </div>
            </div>

            <div class="flex justify-end pt-3">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">save</span>
                    <span>Save Clinic & Financial Template Settings</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Right 1 Col: Staff & Physician Directory -->
    <div class="space-y-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <h2 class="text-xs font-bold text-outline uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">badge</span>
                <span>Physicians & Staff Accounts</span>
            </h2>

            <div class="space-y-3 mb-6">
                <?php foreach ($doctorsList as $doc): ?>
                    <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/30 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <img src="<?= htmlspecialchars($doc['avatar'] ?: 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=200') ?>" class="w-9 h-9 rounded-full object-cover border border-slate-200" alt="Avatar">
                            <div>
                                <p class="font-bold text-on-surface"><?= htmlspecialchars($doc['name']) ?></p>
                                <p class="text-[11px] text-outline"><?= htmlspecialchars($doc['specialty']) ?></p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                            <?= htmlspecialchars($doc['role'] ?? 'Doctor') ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Add Staff Form -->
            <form action="actions/admin_save_doctor.php" method="POST" class="pt-4 border-t border-outline-variant/20 space-y-3 text-xs">
                <p class="font-bold text-slate-700">Add New Doctor / Staff Account</p>
                <div>
                    <input type="text" name="name" required placeholder="Doctor Name (e.g. Dr. Alex Vance)" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>
                <div>
                    <input type="text" name="specialty" placeholder="Specialty (e.g. Neurology)" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>
                <div>
                    <input type="email" name="email" required placeholder="email@clinicflow.com" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password (default: password)" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-medium">
                </div>
                <div>
                    <select name="role" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-semibold">
                        <option value="Doctor">Doctor</option>
                        <option value="Administrator">Administrator</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-2 bg-primary-container text-white text-xs font-semibold rounded-xl hover:bg-primary-container/90 transition shadow-xs">
                    + Add Staff Account
                </button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
