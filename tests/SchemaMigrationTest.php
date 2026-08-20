<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PDO;

class SchemaMigrationTest extends TestCase {
    private PDO $pdo;

    protected function setUp(): void {
        require_once __DIR__ . '/../config/database.php';
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function testExecuteAutoSchemaMigrationsCreatesTables(): void {
        $result = executeAutoSchemaMigrations($this->pdo);

        $this->assertGreaterThan(0, $result['executed']);
        $this->assertEmpty($result['errors']);

        // Verify key tables exist
        $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='doctors'");
        $table = $stmt->fetch();
        $this->assertNotEmpty($table);

        $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='invoices'");
        $table = $stmt->fetch();
        $this->assertNotEmpty($table);
    }

    public function testExecuteAutoSchemaMigrationsIsIdempotent(): void {
        // Run first time
        executeAutoSchemaMigrations($this->pdo);

        // Run second time (should skip already existing statements or re-execute safely)
        $result = executeAutoSchemaMigrations($this->pdo);
        $this->assertEmpty($result['errors']);
    }
}
