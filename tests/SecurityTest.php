<?php

namespace ClinicFlow\Tests;

use PHPUnit\Framework\TestCase;
use PDO;
use ClinicFlow\Services\MigrationRunner;

class SecurityTest extends TestCase {
    private PDO $pdo;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../includes/security.php';

        \executeAutoSchemaMigrations($this->pdo);
        $runner = new MigrationRunner($this->pdo);
        $runner->run();
    }

    public function testLoginRateLimit(): void {
        $ip = '192.168.1.100';
        $email = 'test@clinicflow.org';

        // Clear attempts
        clearLoginAttempts($ip, $email, $this->pdo);

        // Initially should be allowed
        $this->assertTrue(checkLoginRateLimit($ip, $email, $this->pdo));

        // Record 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            recordLoginAttempt($ip, $email, $this->pdo);
        }

        // Now should be rate limited
        $this->assertFalse(checkLoginRateLimit($ip, $email, $this->pdo));

        // Clear attempts
        clearLoginAttempts($ip, $email, $this->pdo);
        $this->assertTrue(checkLoginRateLimit($ip, $email, $this->pdo));
    }
}
