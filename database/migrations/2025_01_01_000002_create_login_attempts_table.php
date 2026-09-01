<?php

return new class {
    public function up(PDO $pdo): void {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id VARCHAR(50) PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(255) NOT NULL,
            attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $pdo->exec($sql);
    }
};
