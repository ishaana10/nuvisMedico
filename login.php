<?php
/**
 * Login Page
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header("Location: index.php");
    exit;
}

$toast = getToast();
?>
<!DOCTYPE html>
<html class="light h-full bg-[#f8f9ff]" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login - ClinicFlow Medical Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full font-sans text-slate-800 bg-[#f8f9ff] flex items-center justify-center p-6">

<?php if ($toast): ?>
<div id="toast-notification" class="fixed top-5 right-5 z-50 flex items-center p-4 text-gray-800 bg-white rounded-xl shadow-lg border border-slate-200" role="alert">
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
<?php endif; ?>

<div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
    <!-- Brand Header -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 p-8 text-white text-center">
        <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mx-auto mb-3 backdrop-blur-xs">
            <span class="material-symbols-outlined text-3xl text-blue-200">medical_services</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight">ClinicFlow</h1>
        <p class="text-xs text-blue-200 mt-1">Medical Center Clinical EHR Platform</p>
    </div>

    <!-- Login Form -->
    <div class="p-8">
        <form action="actions/login.php" method="POST" class="space-y-5 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Email Address</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">mail</span>
                    <input type="email" name="email" value="admin@clinicflow.com" required placeholder="name@clinicflow.com" class="w-full bg-slate-50 pl-10 pr-4 py-3 rounded-xl border border-slate-300 font-medium text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">lock</span>
                    <input type="password" name="password" value="password" required placeholder="••••••••" class="w-full bg-slate-50 pl-10 pr-4 py-3 rounded-xl border border-slate-300 font-medium text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 bg-blue-900 text-white font-bold text-xs rounded-xl hover:bg-blue-800 transition shadow-md flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">login</span>
                    <span>Sign In to Clinical Portal</span>
                </button>
            </div>
        </form>

        <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
            <p class="font-bold text-slate-800">Demo Administrator Credentials:</p>
            <p>Email: <code class="text-blue-700 font-mono font-bold">admin@clinicflow.com</code></p>
            <p>Password: <code class="text-blue-700 font-mono font-bold">password</code></p>
        </div>
    </div>
</div>

</body>
</html>
