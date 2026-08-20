<?php
/**
 * Database Configuration & Connection Manager
 * Dynamically loads credentials from config/config.php or environment variables
 */

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

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
            ensureDoctorColumnsExist($pdo);
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
        ensureDoctorColumnsExist($pdo);
        return $pdo;
    }

    throw new RuntimeException("Unsupported database driver: " . $driver);
}

/**
 * Auto-migrate columns for doctor signatures & stamps if missing
 */
function ensureDoctorColumnsExist(PDO $pdo): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;

    try {
        $cols = [];
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $stmt = $pdo->query("PRAGMA table_info(doctors)");
            while ($row = $stmt->fetch()) {
                $cols[] = strtolower($row['name']);
            }
        } else {
            $stmt = $pdo->query("DESCRIBE doctors");
            while ($row = $stmt->fetch()) {
                $cols[] = strtolower($row['Field']);
            }
        }

        $newCols = [
            'prc_number' => 'VARCHAR(100)',
            'ptr_number' => 'VARCHAR(100)',
            'esignature' => 'TEXT',
            'digital_stamp' => 'TEXT',
            'is_active' => 'INTEGER DEFAULT 1'
        ];

        foreach ($newCols as $colName => $colDef) {
            if (!in_array($colName, $cols)) {
                $pdo->exec("ALTER TABLE doctors ADD COLUMN {$colName} {$colDef}");
            }
        }

        // Auto-migrate visit_id for prescriptions table
        $rxCols = [];
        if ($driver === 'sqlite') {
            $stmt = $pdo->query("PRAGMA table_info(prescriptions)");
            while ($row = $stmt->fetch()) {
                $rxCols[] = strtolower($row['name']);
            }
        } else {
            $stmt = $pdo->query("DESCRIBE prescriptions");
            while ($row = $stmt->fetch()) {
                $rxCols[] = strtolower($row['Field']);
            }
        }

        if (!in_array('visit_id', $rxCols)) {
            $pdo->exec("ALTER TABLE prescriptions ADD COLUMN visit_id VARCHAR(100)");
        }

        // Auto-migrate columns for past_visits table
        $pvCols = [];
        if ($driver === 'sqlite') {
            $stmt = $pdo->query("PRAGMA table_info(past_visits)");
            while ($row = $stmt->fetch()) {
                $pvCols[] = strtolower($row['name']);
            }
        } else {
            $stmt = $pdo->query("DESCRIBE past_visits");
            while ($row = $stmt->fetch()) {
                $pvCols[] = strtolower($row['Field']);
            }
        }

        $newPvCols = [
            'vitals' => 'TEXT',
            'prescriptions' => 'TEXT',
            'soap_notes' => 'TEXT',
            'visit_id' => 'VARCHAR(100)'
        ];

        foreach ($newPvCols as $colName => $colDef) {
            if (!in_array($colName, $pvCols)) {
                $pdo->exec("ALTER TABLE past_visits ADD COLUMN {$colName} {$colDef}");
            }
        }
    } catch (Exception $e) {
        // Table might not exist yet during fresh install
    }
}

/**
 * Execute automatic database schema migrations/updates from SQL schema files.
 */
function executeAutoSchemaMigrations(PDO $pdo): array {
    $results = [
        'executed' => 0,
        'skipped' => 0,
        'errors' => []
    ];

    $schemaFile = __DIR__ . '/../database/schema.sql';
    if (!file_exists($schemaFile)) {
        return $results;
    }

    $sqlContent = file_get_contents($schemaFile);
    if (!$sqlContent) {
        return $results;
    }

    // Split SQL content into individual statements
    $lines = explode("\n", $sqlContent);
    $queries = [];
    $currentQuery = '';

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '--') === 0 || strpos($trimmed, '#') === 0) {
            continue;
        }
        $currentQuery .= $line . "\n";
        if (substr(rtrim($trimmed), -1) === ';') {
            $queries[] = trim($currentQuery);
            $currentQuery = '';
        }
    }

    if (!empty(trim($currentQuery))) {
        $queries[] = trim($currentQuery);
    }

    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    foreach ($queries as $query) {
        if (empty($query)) continue;
        try {
            $pdo->exec($query);
            $results['executed']++;
        } catch (PDOException $e) {
            // Check if error is due to existing table/column/index (benign migration error)
            $msg = $e->getMessage();
            if (
                stripos($msg, 'already exists') !== false ||
                stripos($msg, 'duplicate') !== false ||
                stripos($msg, '1050') !== false || // MySQL Table already exists
                stripos($msg, '1060') !== false || // MySQL Duplicate column name
                stripos($msg, '1061') !== false    // MySQL Duplicate key name
            ) {
                $results['skipped']++;
            } else {
                $results['errors'][] = $msg;
            }
        }
    }

    // Ensure specific column checks are applied
    ensureDoctorColumnsExist($pdo);

    return $results;
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
