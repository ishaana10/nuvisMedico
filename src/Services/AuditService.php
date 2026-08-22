<?php

namespace ClinicFlow\Services;

use PDO;
use ClinicFlow\Utils\Uuid;

class AuditService {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function log(
        string $action,
        ?string $details = null,
        ?string $userId = null,
        ?string $userName = null,
        ?string $userRole = null,
        ?string $clinicId = null
    ): string {
        $id = Uuid::uuidv7();
        $userId = $userId ?? ($_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? 'system');
        $userName = $userName ?? ($_SESSION['user_name'] ?? $_SESSION['user']['name'] ?? 'System');
        $userRole = $userRole ?? ($_SESSION['user_role'] ?? $_SESSION['user']['role'] ?? 'System');
        $clinicId = $clinicId ?? ($_SESSION['clinic_id'] ?? 'default-clinic');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $stmt = $this->db->prepare(
            "INSERT INTO audit_logs (id, clinic_id, user_id, user_name, user_role, action, details, ip_address, created_at) " .
            "VALUES (:id, :clinic_id, :user_id, :user_name, :user_role, :action, :details, :ip, :created_at)"
        );

        $stmt->execute([
            'id' => $id,
            'clinic_id' => $clinicId,
            'user_id' => (string)$userId,
            'user_name' => $userName,
            'user_role' => $userRole,
            'action' => $action,
            'details' => $details,
            'ip' => $ip,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $id;
    }

    public function logClinicalWrite(string $action, string $patientId, array $payload): string {
        return $this->log("CLINICAL_WRITE:" . $action, json_encode(array_merge(['patient_id' => $patientId], $payload)));
    }

    public function logFiscalization(string $invoiceId, string $status, array $payload): string {
        return $this->log("FISCALIZATION:" . $status, json_encode(array_merge(['invoice_id' => $invoiceId], $payload)));
    }

    public function logInventoryChange(string $inventoryId, string $type, int $changeAmount, array $payload = []): string {
        return $this->log("INVENTORY:" . $type, json_encode(array_merge([
            'inventory_id' => $inventoryId,
            'change_amount' => $changeAmount
        ], $payload)));
    }

    public function logAdminAction(string $action, array $payload = []): string {
        return $this->log("ADMIN:" . $action, json_encode($payload));
    }
}
