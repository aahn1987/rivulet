<?php
namespace Rivulet\Database\Migrations;

use Rivulet\Database\Connection;

class Runner
{
    private Connection $connection;
    private string $migrationsPath;
    private array $ran = [];

    public function __construct(Connection $connection, string $migrationsPath)
    {
        $this->connection     = $connection;
        $this->migrationsPath = $migrationsPath;
        $this->createMigrationsTable();
        $this->loadRanMigrations();
    }

    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->connection->pdo()->exec($sql);
    }

    private function loadRanMigrations(): void
    {
        $stmt      = $this->connection->pdo()->query("SELECT migration FROM migrations ORDER BY id");
        $this->ran = $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function run(array $migrations = []): void
    {
        $files = $migrations ?: $this->getMigrationFiles();
        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $className = $this->getClassName($file);

            if (in_array($className, $this->ran)) {
                continue;
            }

            require_once $file;
            $migration = new $className($this->connection);
            $migration->up();

            $this->connection->pdo()->prepare(
                "INSERT INTO migrations (migration, batch) VALUES (?, ?)"
            )->execute([$className, $batch]);

            $this->ran[] = $className;
        }
    }

    public function rollback(int $steps = 1): void
    {
        $migrations = $this->connection->pdo()->query(
            "SELECT migration FROM migrations WHERE batch IN (
                SELECT batch FROM migrations ORDER BY batch DESC LIMIT {$steps}
            ) ORDER BY id DESC"
        )->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($migrations as $migration) {
            $file = $this->findMigrationFile($migration);
            require_once $file;

            $migrationClass = new $migration($this->connection);
            $migrationClass->down();

            $this->connection->pdo()->prepare(
                "DELETE FROM migrations WHERE migration = ?"
            )->execute([$migration]);
        }
    }

    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);

        return $files;
    }

    private function getClassName(string $file): string
    {
        $filename = basename($file, '.php');
        $parts    = explode('_', $filename, 4);

        return isset($parts[3]) ? $parts[3] : $filename;
    }

    private function getNextBatchNumber(): int
    {
        $stmt   = $this->connection->pdo()->query("SELECT MAX(batch) as max FROM migrations");
        $result = $stmt->fetch();

        return ($result['max'] ?? 0) + 1;
    }

    private function findMigrationFile(string $className): string
    {
        $files = glob($this->migrationsPath . "/*_{$className}.php");

        return $files[0] ?? '';
    }
}
