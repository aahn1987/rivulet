<?php
namespace Rivulet\Database\Migrations;

use Rivulet\Database\Connection;

class SeedOperation
{
    public static function execute(array $data, string $table, Connection $connection): void
    {
        foreach ($data as $row) {
            $columns      = implode(', ', array_keys($row));
            $placeholders = implode(', ', array_fill(0, count($row), '?'));

            $sql  = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $connection->pdo()->prepare($sql);
            $stmt->execute(array_values($row));
        }
    }
}
