<?php
namespace ClinicFlow\Repositories;

use PDO;

class PatientRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findAll(int $limit = 50, int $offset = 0, ?string $search = null): array {
        $concatExpr = ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql')
            ? "CONCAT(first_name, ' ', last_name)"
            : "(first_name || ' ' || last_name)";

        if ($search) {
            $stmt = $this->db->prepare("SELECT *, {$concatExpr} AS full_name FROM patients WHERE first_name LIKE ? OR last_name LIKE ? OR mrn LIKE ? OR phone LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
            $searchTerm = "%{$search}%";
            $stmt->bindValue(1, $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(2, $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(3, $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(4, $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(5, $limit, PDO::PARAM_INT);
            $stmt->bindValue(6, $offset, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare("SELECT *, {$concatExpr} AS full_name FROM patients ORDER BY id DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function findById(string $id): ?array {
        $concatExpr = ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql')
            ? "CONCAT(first_name, ' ', last_name)"
            : "(first_name || ' ' || last_name)";

        $stmt = $this->db->prepare("SELECT *, {$concatExpr} AS full_name FROM patients WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByMrn(string $mrn): ?array {
        $concatExpr = ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql')
            ? "CONCAT(first_name, ' ', last_name)"
            : "(first_name || ' ' || last_name)";

        $stmt = $this->db->prepare("SELECT *, {$concatExpr} AS full_name FROM patients WHERE mrn = ? LIMIT 1");
        $stmt->execute([$mrn]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findAllActive(int $limit = 100, int $offset = 0): array {
        return $this->findAll($limit, $offset);
    }

    public function search(string $query, int $limit = 50, int $offset = 0): array {
        return $this->findAll($limit, $offset, $query);
    }

    public function update(string $id, array $data): bool {
        $fields = [];
        $params = [];
        $allowed = ['mrn', 'first_name', 'last_name', 'dob', 'age', 'gender', 'phone', 'email', 'address', 'emergency_contact_name', 'blood_group', 'known_allergies', 'chronic_conditions'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }
        if (empty($fields)) {
            return true;
        }
        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        $sql = "UPDATE patients SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function create(array $data): string {
        $id = $data['id'] ?? ('pat-' . uniqid());
        $stmt = $this->db->prepare("
            INSERT INTO patients (id, mrn, first_name, last_name, dob, age, gender, phone, email, address, emergency_contact_name, blood_group, known_allergies, chronic_conditions, registration_date, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            $id,
            $data['mrn'],
            $data['first_name'] ?? 'FirstName',
            $data['last_name'] ?? 'LastName',
            $data['dob'] ?? '1990-01-01',
            $data['age'] ?? 30,
            $data['gender'] ?? 'Other',
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['emergency_contact_name'] ?? null,
            $data['blood_group'] ?? null,
            $data['known_allergies'] ?? null,
            $data['chronic_conditions'] ?? null
        ]);
        return $id;
    }
}
