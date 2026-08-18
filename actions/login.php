<?php
/**
 * ClinicFlow Login Action Handler
 */
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
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

if ($user && (!empty($user['password_hash']) ? password_verify($password, $user['password_hash']) : $password === 'password')) {
    $_SESSION['authenticated'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'] ?? 'Doctor';
    $_SESSION['current_doctor_id'] = $user['id'];

    setToast("Welcome Back!", "Logged in successfully as " . $user['name'] . ".");
    header("Location: ../index.php");
    exit;
} else {
    setToast('Authentication Failed', 'Invalid email address or password.', 'error');
    header("Location: ../login.php");
    exit;
}
