<?php
namespace ClinicFlow\Services;

use PDO;

class AuditService {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->ensureAuditTable();
    }

    private function ensureAuditTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS audit_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            user_name VARCHAR(100),
            user_role VARCHAR(50),
            action VARCHAR(100),
            details TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            $sql = "CREATE TABLE IF NOT EXISTS audit_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                user_name VARCHAR(100),
                user_role VARCHAR(50),
                action VARCHAR(100),
                details TEXT,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        }
        $this->db->exec($sql);
    }

    public function log(string $action, ?string $details = null, ?int $userId = null, ?string $userName = null, ?string $userRole = null): void {
        $userId = $userId ?? $_SESSION['user_id'] ?? null;
        $userName = $userName ?? $_SESSION['user_name'] ?? 'System';
        $userRole = $userRole ?? $_SESSION['user_role'] ?? 'System';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $stmt = $this->db->prepare("INSERT INTO audit_logs (user_id, user_name, user_role, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
        $stmt->execute([$userId, $userName, $userRole, $action, $details, $ip]);
    }
}
