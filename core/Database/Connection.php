<?php
namespace Rivulet\Database;

use PDO;
use PDOException;

class Connection
{
    private static array $connections = [];
    private PDO $pdo;
    private string $name;

    public function __construct(array $config, string $name = 'default')
    {
        $this->name = $name;
        $this->connect($config);
    }

    private function connect(array $config): void
    {
        $dsn = $this->buildDsn($config);

        try {
            $this->pdo = new PDO(
                $dsn,
                $config['username'] ?? '',
                $config['password'] ?? '',
                $this->getOptions($config)
            );
        } catch (PDOException $e) {
            throw new PDOException("Database connection failed: {$e->getMessage()}");
        }
    }

    private function buildDsn(array $config): string
    {
        $driver   = $config['driver'] ?? 'mysql';
        $host     = $config['host'] ?? '127.0.0.1';
        $port     = $config['port'] ?? '';
        $database = $config['database'] ?? '';
        $charset  = $config['charset'] ?? 'utf8mb4';

        switch ($driver) {
            case 'mysql':
            case 'mariadb':
                return "mysql:host={$host}" . ($port ? ";port={$port}" : "") . ";dbname={$database};charset={$charset}";

            case 'pgsql':
                return "pgsql:host={$host}" . ($port ? ";port={$port}" : "") . ";dbname={$database}";

            case 'sqlite':
                return "sqlite:{$database}";

            default:
                throw new PDOException("Unsupported database driver: {$driver}");
        }
    }

    private function getOptions(array $config): array
    {
        return [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
        ];
    }

    public static function get(array $config, string $name = 'default'): self
    {
        if (! isset(self::$connections[$name])) {
            self::$connections[$name] = new self($config, $name);
        }

        return self::$connections[$name];
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
}
