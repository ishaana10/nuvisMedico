<?php
/**
 * Database Configuration & Connection Manager
 * Supports MySQL for production (A2 Hosting) and SQLite fallback for local testing
 */

define('DB_DRIVER', getenv('DB_DRIVER') ?: 'sqlite'); // 'mysql' or 'sqlite'
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'clinicflow');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('SQLITE_FILE', __DIR__ . '/../database/clinicflow.sqlite');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $driver = DB_DRIVER;

    if ($driver === 'mysql') {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            // Fall back to sqlite if MySQL fails or is not available locally
            $driver = 'sqlite';
        }
    }

    if ($driver === 'sqlite') {
        $dbDir = dirname(SQLITE_FILE);
        if (!file_exists($dbDir)) {
            mkdir($dbDir, 0777, true);
        }
        $pdo = new PDO("sqlite:" . SQLITE_FILE, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec("PRAGMA foreign_keys = ON;");
        return $pdo;
    }

    throw new RuntimeException("Unsupported database driver: " . $driver);
}

// Flash toast notification helper
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function setToast(string $title, string $message, string $type = 'success'): void {
    $_SESSION['toast'] = [
        'id' => uniqid('toast_'),
        'title' => $title,
        'message' => $message,
        'type' => $type
    ];
}

function getToast(): ?array {
    if (isset($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        unset($_SESSION['toast']);
        return $toast;
    }
    return null;
}
