<?php
/**
 * Security & Helper functions (CSRF, RBAC, Rate Limiting, Sanitization, Secure Session)
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie attributes
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

/**
 * Generate CSRF token
 */
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Auto-initialize CSRF token in session
generateCsrfToken();

/**
 * Verify CSRF token
 */
function verifyCsrfToken(?string $token): bool {
    if (!$token || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check CSRF token on POST/PUT/DELETE requests or terminate
 */
function validateCsrfRequest(): void {
    if (in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['POST', 'PUT', 'DELETE'])) {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!verifyCsrfToken($token)) {
            http_response_code(403);
            die("Invalid or missing CSRF token.");
        }
    }
}

/**
 * Require User Authentication
 */
function requireAuth(): void {
    if (empty($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        if (isAjaxRequest()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthenticated']);
            exit;
        }
        header("Location: login.php");
        exit;
    }
}

/**
 * Require Role-based Access
 */
function requireRole(array $allowedRoles): void {
    requireAuth();
    $userRole = $_SESSION['user_role'] ?? 'Doctor';
    if (!in_array($userRole, $allowedRoles, true)) {
        if (isAjaxRequest()) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized role access']);
            exit;
        }
        http_response_code(403);
        die("403 Forbidden: You do not have permission to access this resource.");
    }
}

/**
 * Rate Limiter for Login Attempts
 */
function checkLoginRateLimit(string $ip): bool {
    $maxAttempts = 5;
    $lockoutSeconds = 300; // 5 minutes

    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }

    $now = time();
    // Clean old attempts
    $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($timestamp) use ($now, $lockoutSeconds) {
        return ($now - $timestamp) < $lockoutSeconds;
    });

    return count($_SESSION['login_attempts']) < $maxAttempts;
}

function recordLoginAttempt(): void {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    $_SESSION['login_attempts'][] = time();
}

function clearLoginAttempts(): void {
    unset($_SESSION['login_attempts']);
}

/**
 * Input Sanitization & Escaping
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function isAjaxRequest(): bool {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
           (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}
