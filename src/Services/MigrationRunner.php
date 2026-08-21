<?php

namespace ClinicFlow\Services;

use PDO;
use Exception;

class MigrationRunner {
    private PDO $pdo;
    private string $migrationsDir;

    public function __construct(PDO $pdo, ?string $migrationsDir = null) {
        $this->pdo = $pdo;
        $this->migrationsDir = $migrationsDir ?? __DIR__ . '/../../database/migrations';
    }

    public function run(): array {
        $this->ensureMigrationsTable();
        $applied = $this->getAppliedMigrations();
        $files = glob($this->migrationsDir . '/*.{sql,php}', GLOB_BRACE);

        if (!$files) {
            return [];
        }

        sort($files);
        $executed = [];

        foreach ($files as $file) {
            $version = basename($file);
            if (in_array($version, $applied, true)) {
                continue;
            }

            $this->pdo->beginTransaction();
            try {
                if (str_ends_with($file, '.sql')) {
                    $sql = file_get_contents($file);
                    if ($sql) {
                        $this->pdo->exec($sql);
                    }
                } elseif (str_ends_with($file, '.php')) {
                    $migration = require $file;
                    if (is_callable($migration)) {
                        $migration($this->pdo);
                    } elseif (is_object($migration) && method_exists($migration, 'up')) {
                        $migration->up($this->pdo);
                    }
                }

                $stmt = $this->pdo->prepare("INSERT INTO schema_migrations (version) VALUES (:v)");
                $stmt->execute(['v' => $version]);
                $this->pdo->commit();
                $executed[] = $version;
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw new Exception("Migration {$version} failed: " . $e->getMessage(), 0, $e);
            }
        }

        return $executed;
    }

    private function ensureMigrationsTable(): void {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $sql = "CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(255) PRIMARY KEY,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );";
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(255) PRIMARY KEY,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );";
        }
        $this->pdo->exec($sql);
    }

    public function getAppliedMigrations(): array {
        $this->ensureMigrationsTable();
        $stmt = $this->pdo->query("SELECT version FROM schema_migrations ORDER BY version ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
