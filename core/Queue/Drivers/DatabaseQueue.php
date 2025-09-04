<?php
namespace Rivulet\Queue\Drivers;

use Rivulet\Database\Connection;
use Rivulet\Queue\CallableJob;
use Rivulet\Queue\Job;

class DatabaseQueue implements QueueDriverInterface
{
    private Connection $connection;
    private string $table;

    public function __construct(array $config)
    {
        $this->connection = Connection::get($config['connection'] ?? config('database.connections.mysql'));
        $this->table      = $config['table'] ?? 'jobs';
    }

    public function push($job, string $queue = 'default'): string
    {
        return $this->pushToDatabase(0, $job, $queue);
    }

    public function later(int $delay, $job, string $queue = 'default'): string
    {
        return $this->pushToDatabase(time() + $delay, $job, $queue);
    }

    private function pushToDatabase(int $availableAt, $job, string $queue): string
    {
        $payload = $this->createPayload($job);
        $id      = strRandom(32);

        $this->connection->pdo()->prepare(
            "INSERT INTO {$this->table} (id, queue, payload, attempts, available_at, created_at)
        VALUES (?, ?, ?, 0, ?, ?)"
        )->execute([
            $id,
            $queue,
            $payload,
            $availableAt,
            time(),
        ]);

        return $id;
    }

    private function createPayload($job): string
    {
        if ($job instanceof Job) {
            return serialize($job);
        }

        if (is_callable($job)) {
            return serialize(new CallableJob($job));
        }

        return serialize($job);
    }

    public function pop(string $queue = 'default'): ?Job
    {
        $stmt = $this->connection->pdo()->prepare(
            "SELECT * FROM {$this->table}
             WHERE queue = ? AND available_at <= ?
             ORDER BY created_at ASC
             LIMIT 1"
        );

        $stmt->execute([$queue, time()]);
        $record = $stmt->fetch();

        if (! $record) {
            return null;
        }

        $this->connection->pdo()->prepare(
            "UPDATE {$this->table} SET attempts = attempts + 1 WHERE id = ?"
        )->execute([$record['id']]);

        $job = unserialize($record['payload']);
        $job->setId($record['id']);
        $job->setQueue($record['queue']);
        $job->setAttempts($record['attempts'] + 1);

        return $job;
    }

    public function size(string $queue = 'default'): int
    {
        $stmt = $this->connection->pdo()->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE queue = ? AND available_at <= ?"
        );

        $stmt->execute([$queue, time()]);
        return (int) $stmt->fetchColumn();
    }

    public function clear(string $queue = 'default'): bool
    {
        $stmt = $this->connection->pdo()->prepare(
            "DELETE FROM {$this->table} WHERE queue = ?"
        );

        return $stmt->execute([$queue]);
    }

    public function release(string $id, int $delay = 0): bool
    {
        $stmt = $this->connection->pdo()->prepare(
            "UPDATE {$this->table} SET available_at = ? WHERE id = ?"
        );

        return $stmt->execute([time() + $delay, $id]);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->connection->pdo()->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }

    public function failed(string $id, \Throwable $exception): bool
    {
        $failedTable = $this->table . '_failed';

        $this->connection->pdo()->prepare(
            "INSERT INTO {$failedTable} (id, connection, queue, payload, exception, failed_at)
             VALUES (?, 'database', ?, ?, ?, ?)"
        )->execute([
            $id,
            'default',
            $this->getPayload($id),
            $exception->getMessage(),
            time(),
        ]);

        return $this->delete($id);
    }

    private function getPayload(string $id): string
    {
        $stmt = $this->connection->pdo()->prepare(
            "SELECT payload FROM {$this->table} WHERE id = ?"
        );

        $stmt->execute([$id]);
        return $stmt->fetchColumn() ?: '';
    }

    public function getFailedJobs(): array
    {
        $failedTable = $this->table . '_failed';

        $stmt = $this->connection->pdo()->prepare(
            "SELECT * FROM {$failedTable} ORDER BY failed_at DESC"
        );

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function retry(string $id): bool
    {
        $failedTable = $this->table . '_failed';

        $stmt = $this->connection->pdo()->prepare(
            "SELECT * FROM {$failedTable} WHERE id = ?"
        );

        $stmt->execute([$id]);
        $job = $stmt->fetch();

        if (! $job) {
            return false;
        }

        $this->connection->pdo()->prepare(
            "INSERT INTO {$this->table} (id, queue, payload, attempts, available_at, created_at)
             VALUES (?, ?, ?, 0, ?, ?)"
        )->execute([
            $job['id'],
            $job['queue'],
            $job['payload'],
            time(),
            time(),
        ]);

        $this->connection->pdo()->prepare(
            "DELETE FROM {$failedTable} WHERE id = ?"
        )->execute([$id]);

        return true;
    }

    public function flushFailed(): bool
    {
        $failedTable = $this->table . '_failed';

        return $this->connection->pdo()->exec("DELETE FROM {$failedTable}") !== false;
    }
}
