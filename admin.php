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

        <!-- Continuous System Updates (Git Pull Updater) -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-6 shadow-xs space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 border-b border-outline-variant/20 pb-3">
                <h2 class="text-xs font-bold text-primary uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">update</span>
                    <span>4. Continuous System Updates (1-Click Git Updater)</span>
                </h2>
                <div class="flex gap-2">
                    <button type="button" onclick="switchGitSubTab('console')" id="btn-git-console" class="px-3 py-1 bg-primary text-white text-[11px] font-bold rounded-lg transition">Terminal Status</button>
                    <button type="button" onclick="switchGitSubTab('history')" id="btn-git-history" class="px-3 py-1 bg-surface-container-high text-on-surface text-[11px] font-bold rounded-lg transition">Commit History</button>
                </div>
            </div>

            <!-- Terminal Console Tab -->
            <div id="git-tab-console" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Git Repository Terminal Status</label>
                    <div id="git-status-console" class="font-mono text-xs bg-slate-900 text-emerald-400 p-4 rounded-xl border border-slate-800 h-40 overflow-y-auto whitespace-pre-wrap leading-relaxed shadow-inner">
                        Querying Git repository status...
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Executable Path</label>
                        <input type="text" id="git_path" value="<?= htmlspecialchars($settings['git_path'] ?? 'git') ?>" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-mono text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Repository Directory</label>
                        <input type="text" id="git_repo_dir" value="<?= htmlspecialchars($settings['git_repo_dir'] ?? dirname(__DIR__)) ?>" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-mono text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Update Branch</label>
                        <select id="update_branch" class="w-full bg-surface-container-low px-3 py-2 rounded-xl border border-outline-variant/40 font-bold">
                            <option value="main">main</option>
                            <option value="master">master</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-outline-variant/20">
                    <button type="button" onclick="saveGitSettings()" class="px-4 py-2 bg-surface-container-high text-on-surface text-xs font-semibold rounded-xl hover:bg-surface-variant transition flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">settings</span>
                        <span>Save Git Settings</span>
                    </button>

                    <div class="flex gap-2">
                        <button type="button" onclick="refreshGitStatus()" class="px-4 py-2 bg-slate-200 text-slate-800 text-xs font-semibold rounded-xl hover:bg-slate-300 transition flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">sync</span>
                            <span>Check Status</span>
                        </button>
                        <button type="button" onclick="triggerGitPull()" class="px-5 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition shadow-xs flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">download</span>
                            <span>Pull Updates from Git</span>
                        </button>
                    </div>
                </div>

                <!-- Initialize / Link Git Repo Form (if missing) -->
                <div id="git-init-card" class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 space-y-3">
                    <p class="font-bold text-amber-900 text-xs flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-amber-700">link</span>
                        <span>Link Remote Git Repository</span>
                    </p>
                    <div class="flex gap-2">
                        <input type="text" id="git_remote_url" placeholder="https://github.com/username/repository.git" value="<?= htmlspecialchars($settings['git_remote_url'] ?? '') ?>" class="flex-1 bg-white px-3 py-2 rounded-xl border border-amber-300 text-xs font-mono">
                        <button type="button" onclick="initializeGitRepo()" class="px-4 py-2 bg-amber-800 text-white font-bold rounded-xl text-xs hover:bg-amber-900 transition">
                            Link & Sync
                        </button>
                    </div>
                </div>
            </div>

            <!-- Commit History Tab -->
            <div id="git-tab-history" class="hidden space-y-3 text-xs">
                <div id="git-commit-list" class="space-y-2 max-h-60 overflow-y-auto">
                    <p class="text-slate-500 italic">Loading commit logs...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right 1 Col: Staff & Physician Directory -->
    <div class="space-y-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xs font-bold text-outline uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">badge</span>
                    <span>Physicians & Credentials</span>
                </h2>
                <button type="button" onclick="openDoctorModal()" class="px-3 py-1.5 bg-primary text-white text-[11px] font-bold rounded-lg hover:bg-primary/90 transition flex items-center gap-1 shadow-xs">
                    <span class="material-symbols-outlined text-xs">add</span>
                    <span>Add Doctor</span>
                </button>
            </div>

            <div class="space-y-3 mb-6">
                <?php foreach ($doctorsList as $doc): ?>
                    <div class="p-3.5 rounded-xl bg-surface-container-low border border-outline-variant/30 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="<?= htmlspecialchars($doc['avatar'] ?: 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=200') ?>" class="w-10 h-10 rounded-full object-cover border border-slate-200" alt="Avatar">
                                <div>
                                    <p class="font-bold text-on-surface"><?= htmlspecialchars($doc['name']) ?></p>
                                    <p class="text-[11px] text-outline"><?= htmlspecialchars($doc['specialty']) ?></p>
                                </div>
                            </div>
                            <button type="button" onclick='openDoctorModal(<?= htmlspecialchars(json_encode($doc), ENT_QUOTES, "UTF-8") ?>)' class="p-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition" title="Edit Doctor & Credentials">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                        </div>

                        <div class="pt-2 border-t border-slate-200/60 grid grid-cols-2 gap-2 text-[10px] text-slate-600 font-mono">
                            <div><span class="text-slate-400">PRC:</span> <?= htmlspecialchars($doc['prc_number'] ?? 'N/A') ?></div>
                            <div><span class="text-slate-400">PTR:</span> <?= htmlspecialchars($doc['ptr_number'] ?? 'N/A') ?></div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <?php if (!empty($doc['esignature'])): ?>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">draw</span> Signature
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($doc['digital_stamp'])): ?>
                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">verified</span> Digital Stamp
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function refreshGitStatus() {
    const consoleBox = document.getElementById('git-status-console');
    if (!consoleBox) return;
    consoleBox.innerText = "Querying Git repository status...";

    fetch('actions/git_actions.php?action=git_status')
    .then(r => r.json())
    .then(d => {
        if (d.git_path && document.getElementById('git_path')) document.getElementById('git_path').value = d.git_path;
        if (d.git_repo_dir && document.getElementById('git_repo_dir')) document.getElementById('git_repo_dir').value = d.git_repo_dir;
        if (d.git_remote_url && document.getElementById('git_remote_url')) document.getElementById('git_remote_url').value = d.git_remote_url;

        if (d.remote_branches && d.remote_branches.length > 0) {
            const select = document.getElementById('update_branch');
            if (select) {
                select.innerHTML = '';
                d.remote_branches.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b;
                    opt.textContent = b;
                    if (b === d.selected_branch) opt.selected = true;
                    select.appendChild(opt);
                });
            }
        }

        if (d.success) {
            consoleBox.innerHTML = `<span class="text-emerald-400 font-bold">✔ Git Repository Active</span>\nBranch: ${d.branch || 'main'}\n\n${d.status}`;
        } else {
            consoleBox.innerHTML = `<span class="text-amber-400 font-bold">⚠ Git Repository Not Linked or Unreachable</span>\n\n${d.status || d.error || 'Repository not initialized.'}`;
        }
    })
    .catch(err => {
        consoleBox.innerText = "Error querying Git status: " + err;
    });
}

function saveGitSettings() {
    const gitPath = document.getElementById('git_path').value;
    const gitRepoDir = document.getElementById('git_repo_dir').value;
    const updateBranch = document.getElementById('update_branch').value;

    fetch('actions/git_actions.php?action=save_git_settings', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({git_path: gitPath, git_repo_dir: gitRepoDir, update_branch: updateBranch})
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert('Git settings saved successfully.');
            refreshGitStatus();
        } else {
            alert('Failed to save Git settings: ' + (d.error || 'Unknown error'));
        }
    });
}

function triggerGitPull() {
    const branch = document.getElementById('update_branch').value || 'main';
    const consoleBox = document.getElementById('git-status-console');
    if (!confirm(`Are you sure you want to pull updates from branch '${branch}'? Local files will be updated.`)) return;

    consoleBox.innerText = `Executing 'git pull origin ${branch}'... Please wait...`;

    fetch('actions/git_actions.php?action=git_pull', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({branch: branch})
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert('Git pull completed successfully!');
            consoleBox.innerHTML = `<span class="text-emerald-400 font-bold">✔ Git Pull Successful</span>\n\n${d.output}`;
            loadGitHistory();
        } else {
            alert('Git pull failed: ' + (d.error || 'Unknown error'));
            consoleBox.innerHTML = `<span class="text-red-400 font-bold">❌ Git Pull Error</span>\n\n${d.error || 'Pull failed'}`;
        }
    });
}

function initializeGitRepo() {
    const repoUrl = document.getElementById('git_remote_url').value;
    if (!repoUrl) {
        alert('Please enter a remote Git URL (e.g. https://github.com/username/repository.git)');
        return;
    }

    if (!confirm('This will initialize a Git repository in your directory and link it to remote origin. Proceed?')) return;

    fetch('actions/git_actions.php?action=git_init', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({repo_url: repoUrl})
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            alert('Git repository linked successfully!');
            refreshGitStatus();
        } else {
            alert('Git init failed: ' + (d.error || 'Unknown error'));
        }
    });
}

function switchGitSubTab(tab) {
    if (tab === 'console') {
        document.getElementById('git-tab-console').classList.remove('hidden');
        document.getElementById('git-tab-history').classList.add('hidden');
        document.getElementById('btn-git-console').className = 'px-3 py-1 bg-primary text-white text-[11px] font-bold rounded-lg transition';
        document.getElementById('btn-git-history').className = 'px-3 py-1 bg-surface-container-high text-on-surface text-[11px] font-bold rounded-lg transition';
    } else {
        document.getElementById('git-tab-console').classList.add('hidden');
        document.getElementById('git-tab-history').classList.remove('hidden');
        document.getElementById('btn-git-console').className = 'px-3 py-1 bg-surface-container-high text-on-surface text-[11px] font-bold rounded-lg transition';
        document.getElementById('btn-git-history').className = 'px-3 py-1 bg-primary text-white text-[11px] font-bold rounded-lg transition';
        loadGitHistory();
    }
}

function loadGitHistory() {
    const list = document.getElementById('git-commit-list');
    list.innerHTML = '<p class="text-slate-500 italic">Fetching commit history...</p>';

    fetch('actions/git_actions.php?action=git_log')
    .then(r => r.json())
    .then(d => {
        if (d.success && d.commits && d.commits.length > 0) {
            list.innerHTML = d.commits.map(c => `
                <div class="p-3 rounded-xl bg-surface-container-low border border-outline-variant/30 text-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-mono font-bold text-primary">${c.hash}</span>
                        <span class="text-[10px] text-outline">${c.date}</span>
                    </div>
                    <p class="font-bold text-on-surface">${c.message}</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Author: ${c.author}</p>
                </div>
            `).join('');
        } else {
            list.innerHTML = `<p class="text-slate-500 italic p-3">${d.error || 'No commits found.'}</p>`;
        }
    });
}

document.addEventListener('DOMContentLoaded', refreshGitStatus);
</script>

<!-- Modal: Doctor Add/Edit & Credentials Upload -->
<div id="doctorModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8">
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
            <h3 id="docModalTitle" class="font-bold text-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">badge</span>
                <span>Manage Doctor Credentials & Signatures</span>
            </h3>
            <button type="button" onclick="closeDoctorModal()" class="text-slate-400 hover:text-white text-lg">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="actions/doctor_actions.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="doctor_id" id="doc_id">
            <input type="hidden" name="existing_esignature" id="doc_existing_esignature">
            <input type="hidden" name="existing_digital_stamp" id="doc_existing_digital_stamp">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Doctor Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="doc_name" required placeholder="Dr. Sarah Jenkins" class="w-full bg-slate-50 px-3 py-2 rounded-xl border border-slate-300 font-bold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Specialty</label>
                    <input type="text" name="specialty" id="doc_specialty" placeholder="Internal Medicine / General Practice" class="w-full bg-slate-50 px-3 py-2 rounded-xl border border-slate-300 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="doc_email" required placeholder="sjenkins@clinicflow.com" class="w-full bg-slate-50 px-3 py-2 rounded-xl border border-slate-300 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="doc_password" placeholder="Leave blank to keep unchanged" class="w-full bg-slate-50 px-3 py-2 rounded-xl border border-slate-300 font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">PRC License Number</label>
                    <input type="text" name="prc_number" id="doc_prc" placeholder="PRC-0098412" class="w-full bg-slate-50 px-3 py-2 rounded-xl border border-slate-300 font-mono font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">PTR Number</label>
                    <input type="text" name="ptr_number" id="doc_ptr" placeholder="PTR-8842109" class="w-full bg-slate-50 px-3 py-2 rounded-xl border border-slate-300 font-mono font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role</label>
                    <select name="role" id="doc_role" class="w-full bg-slate-50 px-3 py-2 rounded-xl border border-slate-300 font-semibold">
                        <option value="Doctor">Doctor</option>
                        <option value="Administrator">Administrator</option>
                        <option value="Developer">Developer</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Avatar Image URL</label>
                    <input type="text" name="avatar" id="doc_avatar" placeholder="https://..." class="w-full bg-slate-50 px-3 py-2 rounded-xl border border-slate-300 font-medium">
                </div>
            </div>

            <hr class="border-slate-200">

            <!-- E-Signature Section -->
            <div class="space-y-2">
                <label class="block font-bold text-slate-700">Official E-Signature (Image File or Data URL)</label>
                <div class="flex items-center gap-3">
                    <input type="file" name="esignature_file" accept="image/*" class="text-xs text-slate-600">
                    <span class="text-xs text-slate-400 font-semibold">OR Data String:</span>
                </div>
                <textarea name="esignature_data" id="doc_esignature_data" rows="2" placeholder="data:image/png;base64,..." class="w-full bg-slate-50 p-2.5 rounded-xl border border-slate-300 font-mono text-[11px]"></textarea>
                <div id="esignature_preview" class="p-2 border border-slate-200 rounded-xl bg-slate-50 hidden">
                    <p class="text-[10px] text-slate-500 font-bold mb-1">E-Signature Preview:</p>
                    <img id="img_esignature_preview" src="" class="h-12 object-contain" alt="Signature Preview">
                </div>
            </div>

            <!-- Digital Stamp Section -->
            <div class="space-y-2">
                <label class="block font-bold text-slate-700">Official Digital Stamp (Image File or Data URL)</label>
                <div class="flex items-center gap-3">
                    <input type="file" name="stamp_file" accept="image/*" class="text-xs text-slate-600">
                    <span class="text-xs text-slate-400 font-semibold">OR Data String:</span>
                </div>
                <textarea name="digital_stamp_data" id="doc_digital_stamp_data" rows="2" placeholder="data:image/png;base64,..." class="w-full bg-slate-50 p-2.5 rounded-xl border border-slate-300 font-mono text-[11px]"></textarea>
                <div id="stamp_preview" class="p-2 border border-slate-200 rounded-xl bg-slate-50 hidden">
                    <p class="text-[10px] text-slate-500 font-bold mb-1">Digital Stamp Preview:</p>
                    <img id="img_stamp_preview" src="" class="h-16 object-contain" alt="Stamp Preview">
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                <button type="button" onclick="closeDoctorModal()" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-xl hover:bg-slate-300 transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition shadow-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">save</span>
                    <span>Save Physician Record</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDoctorModal(doc = null) {
    const modal = document.getElementById('doctorModal');
    modal.classList.remove('hidden');

    if (doc) {
        document.getElementById('docModalTitle').innerText = 'Edit Physician & Credentials';
        document.getElementById('doc_id').value = doc.id || '';
        document.getElementById('doc_name').value = doc.name || '';
        document.getElementById('doc_specialty').value = doc.specialty || '';
        document.getElementById('doc_email').value = doc.email || '';
        document.getElementById('doc_password').value = '';
        document.getElementById('doc_prc').value = doc.prc_number || '';
        document.getElementById('doc_ptr').value = doc.ptr_number || '';
        document.getElementById('doc_role').value = doc.role || 'Doctor';
        document.getElementById('doc_avatar').value = doc.avatar || '';

        document.getElementById('doc_existing_esignature').value = doc.esignature || '';
        document.getElementById('doc_esignature_data').value = doc.esignature || '';
        if (doc.esignature) {
            document.getElementById('esignature_preview').classList.remove('hidden');
            document.getElementById('img_esignature_preview').src = doc.esignature;
        } else {
            document.getElementById('esignature_preview').classList.add('hidden');
        }

        document.getElementById('doc_existing_digital_stamp').value = doc.digital_stamp || '';
        document.getElementById('doc_digital_stamp_data').value = doc.digital_stamp || '';
        if (doc.digital_stamp) {
            document.getElementById('stamp_preview').classList.remove('hidden');
            document.getElementById('img_stamp_preview').src = doc.digital_stamp;
        } else {
            document.getElementById('stamp_preview').classList.add('hidden');
        }
    } else {
        document.getElementById('docModalTitle').innerText = 'Add New Physician Account';
        document.getElementById('doc_id').value = '';
        document.getElementById('doc_name').value = '';
        document.getElementById('doc_specialty').value = '';
        document.getElementById('doc_email').value = '';
        document.getElementById('doc_password').value = '';
        document.getElementById('doc_prc').value = '';
        document.getElementById('doc_ptr').value = '';
        document.getElementById('doc_role').value = 'Doctor';
        document.getElementById('doc_avatar').value = '';
        document.getElementById('doc_existing_esignature').value = '';
        document.getElementById('doc_esignature_data').value = '';
        document.getElementById('esignature_preview').classList.add('hidden');
        document.getElementById('doc_existing_digital_stamp').value = '';
        document.getElementById('doc_digital_stamp_data').value = '';
        document.getElementById('stamp_preview').classList.add('hidden');
    }
}

function closeDoctorModal() {
    document.getElementById('doctorModal').classList.add('hidden');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
