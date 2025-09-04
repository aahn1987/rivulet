<?php
namespace Rivulet\Database\Operations;

class DeleteColumn
{
    public static function execute(string $table, string $column)
    {
        return "ALTER TABLE {$table} DROP COLUMN {$column}";
    }
}
