<?php
$pageTitle = "Register New Patient - ClinicFlow";
$activePage = "register-patient";
include __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Header Title -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-on-surface">New Patient Registration</h1>
            <p class="text-xs text-outline font-medium">Create a new medical chart and generate patient MRN file</p>
        </div>
        <a href="patients.php" class="px-3.5 py-2 bg-surface-container-high text-on-surface text-xs font-semibold rounded-xl hover:bg-surface-container-highest transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            <span>Back to Directory</span>
        </a>
    </div>

    <!-- Registration Form Card -->
    <form action="actions/patient_add.php" method="POST" class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-6 shadow-xs space-y-6">

        <!-- Section 1: Demographics -->
        <div>
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-base">person</span>
                <span>1. Personal & Demographics</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" required placeholder="e.g. Sarah" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" required placeholder="e.g. Jenkins" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                    <input type="date" name="dob" required class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" required class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" required placeholder="(555) 000-0000" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="patient@example.com" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Residential Address</label>
                    <input type="text" name="address" placeholder="Street Address, City, State, ZIP" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>
            </div>
        </div>

        <hr class="border-outline-variant/20">

        <!-- Section 2: Emergency Contact & Insurance -->
        <div>
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-base">shield</span>
                <span>2. Emergency Contact & Insurance</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" placeholder="Name" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Relationship</label>
                    <input type="text" name="emergency_contact_relationship" placeholder="e.g. Spouse / Parent" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Emergency Phone</label>
                    <input type="tel" name="emergency_contact_phone" placeholder="(555) 000-0000" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Insurance Provider</label>
                    <input type="text" name="insurance_provider" placeholder="e.g. Blue Cross" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Policy Number</label>
                    <input type="text" name="insurance_policy_number" placeholder="Policy #" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Group Number</label>
                    <input type="text" name="insurance_group_number" placeholder="Group #" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>
            </div>
        </div>

        <hr class="border-outline-variant/20">

        <!-- Section 3: Clinical Background & Allergies -->
        <div>
            <h2 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-base">medical_information</span>
                <span>3. Medical Profile & Allergies</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Known Allergies</label>
                    <input type="text" name="known_allergies" placeholder="e.g. Penicillin, Latex (or None)" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Blood Group</label>
                    <select name="blood_group" class="w-full bg-surface-container-low px-3.5 py-2.5 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium">
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Chronic Conditions & Baseline Notes</label>
                    <textarea name="chronic_conditions" rows="3" placeholder="Document any existing medical conditions..." class="w-full bg-surface-container-low p-3 rounded-xl border border-outline-variant/40 focus:border-primary focus:bg-white focus:outline-none font-medium"></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant/20">
            <a href="patients.php" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-200 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-primary text-white text-xs font-semibold rounded-xl hover:bg-primary/90 transition shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-base">how_to_reg</span>
                <span>Register & Open Chart</span>
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
