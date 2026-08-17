<?php
/**
 * ClinicFlow Web Installation Wizard
 * Sets up MySQL Database, User Credentials, Administrator Profile, creates Tables and Seeds Initial Data
 */
session_start();

$step = $_GET['step'] ?? 1;
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbDriver = $_POST['db_driver'] ?? 'mysql';
    $dbHost   = trim($_POST['db_host'] ?? 'localhost');
    $dbPort   = trim($_POST['db_port'] ?? '3306');
    $dbName   = trim($_POST['db_name'] ?? 'clinicflow');
    $dbUser   = trim($_POST['db_user'] ?? 'root');
    $dbPass   = $_POST['db_pass'] ?? '';

    $adminName = trim($_POST['admin_name'] ?? 'Dr. Sarah Jenkins');
    $adminSpec = trim($_POST['admin_specialty'] ?? 'Internal Medicine');

    if ($dbDriver === 'mysql') {
        try {
            // First test connection to MySQL server
            $dsnNoDb = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
            $pdoTest = new PDO($dsnNoDb, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Attempt to create database if it doesn't exist
            $pdoTest->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            // Connect to specific database
            $dsnWithDb = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsnWithDb, $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Run Schema Creation
            $schemaFile = __DIR__ . '/database/schema.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                $pdo->exec($sql);
            }

            // Seed Initial Administrator & Default Data
            $stmtDoc = $pdo->prepare("INSERT INTO doctors (id, name, specialty, color, dot_color_class, avatar) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), specialty = VALUES(specialty)");
            $stmtDoc->execute([
                'doc-1', $adminName, $adminSpec, '#10B981', 'bg-emerald-500', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=200'
            ]);

            // Also seed other initial mock data if tables are empty
            $patCount = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
            if ($patCount == 0) {
                require_once __DIR__ . '/database/seed.php';
            }

            // Write Config File
            $configContent = "<?php\n" .
                "/**\n * Auto-generated ClinicFlow Configuration\n */\n" .
                "return [\n" .
                "    'db_driver' => " . var_export($dbDriver, true) . ",\n" .
                "    'db_host'   => " . var_export($dbHost, true) . ",\n" .
                "    'db_port'   => " . var_export($dbPort, true) . ",\n" .
                "    'db_name'   => " . var_export($dbName, true) . ",\n" .
                "    'db_user'   => " . var_export($dbUser, true) . ",\n" .
                "    'db_pass'   => " . var_export($dbPass, true) . ",\n" .
                "    'admin_doctor' => [\n" .
                "        'name'      => " . var_export($adminName, true) . ",\n" .
                "        'specialty' => " . var_export($adminSpec, true) . ",\n" .
                "    ]\n" .
                "];\n";

            file_put_contents(__DIR__ . '/config/config.php', $configContent);

            $success = "Database installation completed successfully! Configuration saved to config/config.php.";
            $step = 2;

        } catch (Exception $e) {
            $error = "Database setup failed: " . $e->getMessage();
        }
    } else {
        // SQLite mode
        try {
            require_once __DIR__ . '/database/seed.php';
            $configContent = "<?php\n" .
                "return [\n" .
                "    'db_driver' => 'sqlite',\n" .
                "    'admin_doctor' => ['name' => " . var_export($adminName, true) . ", 'specialty' => " . var_export($adminSpec, true) . "]\n" .
                "];\n";
            file_put_contents(__DIR__ . '/config/config.php', $configContent);
            $success = "SQLite Database initialized successfully!";
            $step = 2;
        } catch (Exception $e) {
            $error = "Error initializing SQLite: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html class="light h-full bg-[#f8f9ff]" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>ClinicFlow Setup & Database Installer</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full font-sans text-slate-800 bg-[#f8f9ff] flex items-center justify-center p-6">

<div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-blue-900 p-6 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl text-blue-200">settings_suggest</span>
            </div>
            <div>
                <h1 class="text-xl font-bold">ClinicFlow Installation Wizard</h1>
                <p class="text-xs text-blue-200">Configure MySQL Database, User Credentials & Administrator Profile</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full bg-blue-800 text-blue-100 text-xs font-semibold">
            PHP 8.1+
        </span>
    </div>

    <div class="p-8">
        <?php if ($error): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs flex items-center gap-3">
                <span class="material-symbols-outlined text-red-600 text-xl">error</span>
                <div>
                    <strong class="font-bold block">Installation Error</strong>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($step == 2): ?>
            <div class="p-6 text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                    <span class="material-symbols-outlined text-3xl">check_circle</span>
                </div>
                <h2 class="text-xl font-bold text-slate-900">Setup Complete!</h2>
                <p class="text-xs text-slate-600 max-w-md mx-auto">
                    Your database has been initialized, tables created, seed data imported, and credentials saved to <code class="bg-slate-100 px-2 py-0.5 rounded font-mono text-blue-800">config/config.php</code>.
                </p>
                <div class="pt-4">
                    <a href="index.php" class="px-6 py-3 bg-blue-600 text-white font-semibold text-xs rounded-xl hover:bg-blue-700 transition inline-flex items-center gap-2 shadow-sm">
                        <span>Launch Clinic Dashboard</span>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form action="install.php" method="POST" class="space-y-6 text-xs">

                <!-- Database Type -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-blue-900 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">database</span>
                        <span>1. Database Credentials & Server</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Database Engine</label>
                            <select name="db_driver" class="w-full bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-300 font-semibold focus:outline-none focus:border-blue-600">
                                <option value="mysql">MySQL (A2 Hosting / Production)</option>
                                <option value="sqlite">SQLite (Local Dev Mode)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Database Host</label>
                            <input type="text" name="db_host" value="localhost" required placeholder="e.g. localhost or 127.0.0.1" class="w-full bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-300 font-medium focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Database Name</label>
                            <input type="text" name="db_name" value="clinicflow" required placeholder="e.g. clinicflow" class="w-full bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-300 font-medium focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Port</label>
                            <input type="text" name="db_port" value="3306" required placeholder="3306" class="w-full bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-300 font-medium focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Database Username</label>
                            <input type="text" name="db_user" value="root" required placeholder="Database Username" class="w-full bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-300 font-medium focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Database Password</label>
                            <input type="password" name="db_pass" placeholder="Leave empty if none" class="w-full bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-300 font-medium focus:outline-none focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <hr class="border-slate-200">

                <!-- Administrator Profile -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-blue-900 mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">manage_accounts</span>
                        <span>2. Administrator & Chief Physician Profile</span>
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Doctor Full Name</label>
                            <input type="text" name="admin_name" value="Dr. Sarah Jenkins" required placeholder="Dr. First Last" class="w-full bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-300 font-medium focus:outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Specialty</label>
                            <input type="text" name="admin_specialty" value="Internal Medicine" required placeholder="e.g. Internal Medicine" class="w-full bg-slate-50 px-3 py-2.5 rounded-xl border border-slate-300 font-medium focus:outline-none focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-slate-200">
                    <span class="text-[11px] text-slate-500">Will create database tables and import seed records.</span>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-bold text-xs rounded-xl hover:bg-blue-700 transition shadow-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">save</span>
                        <span>Save Config & Install Database</span>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
