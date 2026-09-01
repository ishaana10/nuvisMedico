<?php

return new class {
    public function up(PDO $pdo): void {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        // 1. Create clinics table
        $pdo->exec("CREATE TABLE IF NOT EXISTS clinics (
            id VARCHAR(50) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(50) UNIQUE NOT NULL,
            address TEXT,
            phone VARCHAR(50),
            email VARCHAR(255),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );");

        // Insert default clinic if not exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clinics WHERE id = 'default-clinic'");
        $stmt->execute();
        if ((int)$stmt->fetchColumn() === 0) {
            $insert = $pdo->prepare("INSERT INTO clinics (id, name, code, address, phone, email) VALUES (:id, :name, :code, :address, :phone, :email)");
            $insert->execute([
                'id' => 'default-clinic',
                'name' => 'Main Suva Central Clinic',
                'code' => 'SUVA-MAIN',
                'address' => '2 Woodstand Road, Suva',
                'phone' => '+679 330 1234',
                'email' => 'suva@clinicflow.org'
            ]);
        }

        // 2. Add clinic_id to all relevant tables
        $tables = [
            'doctors',
            'patients',
            'appointments',
            'queue',
            'vitals',
            'soap_notes',
            'prescriptions',
            'past_visits',
            'activities',
            'invoices',
            'invoice_items',
            'vms_logs',
            'inventory',
            'inventory_logs',
            'medical_certificates',
            'audit_logs'
        ];

        foreach ($tables as $table) {
            // Check existing columns
            $cols = [];
            try {
                if ($driver === 'sqlite') {
                    $check = $pdo->query("PRAGMA table_info({$table})");
                    while ($row = $check->fetch()) {
                        $cols[] = strtolower($row['name']);
                    }
                } else {
                    $check = $pdo->query("DESCRIBE {$table}");
                    while ($row = $check->fetch()) {
                        $cols[] = strtolower($row['Field']);
                    }
                }
            } catch (Exception $e) {
                continue; // Table might not exist yet
            }

            if (!empty($cols) && !in_array('clinic_id', $cols, true)) {
                $pdo->exec("ALTER TABLE {$table} ADD COLUMN clinic_id VARCHAR(50) DEFAULT 'default-clinic'");
            }

            // Ensure null or blank values are assigned default-clinic
            if (!empty($cols)) {
                $pdo->exec("UPDATE {$table} SET clinic_id = 'default-clinic' WHERE clinic_id IS NULL OR clinic_id = ''");
            }
        }
    }
};
