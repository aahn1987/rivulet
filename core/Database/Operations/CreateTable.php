<?php
namespace Rivulet\Database\Operations;

class CreateTable
{
    public static function execute(string $table, array $columns)
    {
        $sql = "CREATE TABLE {$table} (" . implode(', ', $columns) . ")";
        return $sql;
    }
}
