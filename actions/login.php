<?php
/**
 * ClinicFlow Login Action Handler
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (!checkLoginRateLimit($clientIp)) {
    setToast('Too Many Attempts', 'Too many failed login attempts. Please wait 5 minutes before trying again.', 'error');
    header("Location: ../login.php");
    exit;
}

validateCsrfRequest();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    recordLoginAttempt();
    setToast('Login Error', 'Please enter both email and password.', 'error');
    header("Location: ../login.php");
    exit;
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && isset($user['is_active']) && (int)$user['is_active'] === 0) {
    setToast('Account Disabled', 'Your user account has been deactivated. Please contact an administrator.', 'error');
    header("Location: ../login.php");
    exit;
}

$valid = false;
if ($user && !empty($user['password_hash'])) {
    $valid = password_verify($password, $user['password_hash']);
}

if ($valid && $user) {
    clearLoginAttempts();
    session_regenerate_id(true);

    $_SESSION['authenticated'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'] ?? 'Doctor';
    $_SESSION['current_doctor_id'] = $user['id'];

    // If Developer or Administrator, automatically trigger backend database schema update
    $userRole = $_SESSION['user_role'];
    if (in_array($userRole, ['Developer', 'Administrator'])) {
        $migrationStats = executeAutoSchemaMigrations($pdo);
        if ($migrationStats['executed'] > 0) {
            setToast("Welcome Back!", "Logged in as " . $user['name'] . ". Database schema automatically updated (" . $migrationStats['executed'] . " statements executed).");
        } else {
            setToast("Welcome Back!", "Logged in successfully as " . $user['name'] . ".");
        }
    } else {
        setToast("Welcome Back!", "Logged in successfully as " . $user['name'] . ".");
    }

    header("Location: ../index.php");
    exit;
} else {
    recordLoginAttempt();
    setToast('Authentication Failed', 'Invalid email address or password.', 'error');
    header("Location: ../login.php");
    exit;
}
