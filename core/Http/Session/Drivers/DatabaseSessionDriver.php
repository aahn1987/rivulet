<?php
namespace Rivulet\Http\Session\Drivers;

use Rivulet\Database\Connection;
use Rivulet\Http\Session\SessionDriverInterface;

class DatabaseSessionDriver implements SessionDriverInterface
{
    private Connection $connection;
    private string $table;
    private string $id;
    private array $data = [];

    public function __construct(array $config)
    {
        $this->connection = Connection::get($config['connection'] ?? config('database.connections.mysql'));
        $this->table      = $config['table'] ?? 'sessions';
    }

    public function start(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $stmt = $this->connection->pdo()->prepare(
            "SELECT * FROM {$this->table} WHERE id = ? AND last_activity > ?"
        );

        $stmt->execute([
            $this->id,
            time() - ($this->config['lifetime'] ?? 120 * 60),
        ]);

        $record = $stmt->fetch();

        if ($record) {
            $this->data = unserialize($record['payload']) ?: [];
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function put(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->saveData();
    }

    public function forget(string $key): void
    {
        unset($this->data[$key]);
        $this->saveData();
    }

    public function flush(): void
    {
        $this->data = [];
        $this->saveData();
    }

    public function all(): array
    {
        return $this->data;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function regenerate(string $newId): void
    {
        $this->connection->pdo()->prepare(
            "UPDATE {$this->table} SET id = ? WHERE id = ?"
        )->execute([$newId, $this->id]);

        $this->id = $newId;
    }

    private function saveData(): void
    {
        $payload = serialize($this->data);

        $stmt = $this->connection->pdo()->prepare(
            "INSERT INTO {$this->table} (id, payload, last_activity)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE payload = ?, last_activity = ?"
        );

        $stmt->execute([
            $this->id,
            $payload,
            time(),
            $payload,
            time(),
        ]);
    }
}
