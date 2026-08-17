<?php
/**
 * Database Configuration & Connection Manager
 * Dynamically loads credentials from config/config.php or environment variables
 */

function getAppConfig(): array {
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $configFile = __DIR__ . '/config.php';
    if (file_exists($configFile)) {
        $config = require $configFile;
    } else {
        // Fallback default configuration
        $config = [
            'db_driver' => getenv('DB_DRIVER') ?: 'sqlite',
            'db_host'   => getenv('DB_HOST')   ?: '127.0.0.1',
            'db_port'   => getenv('DB_PORT')   ?: '3306',
            'db_name'   => getenv('DB_NAME')   ?: 'clinicflow',
            'db_user'   => getenv('DB_USER')   ?: 'root',
            'db_pass'   => getenv('DB_PASS')   ?: '',
        ];
    }
    return $config;
}

define('SQLITE_FILE', __DIR__ . '/../database/clinicflow.sqlite');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $cfg = getAppConfig();
    $driver = $cfg['db_driver'] ?? 'mysql';

    if ($driver === 'mysql') {
        try {
            $host = $cfg['db_host'] ?? '127.0.0.1';
            $port = $cfg['db_port'] ?? '3306';
            $dbname = $cfg['db_name'] ?? 'clinicflow';
            $user = $cfg['db_user'] ?? 'root';
            $pass = $cfg['db_pass'] ?? '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            // Fallback to SQLite if MySQL is unavailable locally in dev mode
            if (!file_exists(__DIR__ . '/config.php')) {
                $driver = 'sqlite';
            } else {
                throw $e;
            }
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

// Session & Toast helper
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
