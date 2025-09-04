<?php
namespace Rivulet\Database\Operations;

class AlterTable
{
    public static function execute(string $table, array $changes)
    {
        $sql = "ALTER TABLE {$table} " . implode(', ', $changes);
        return $sql;
    }
}
