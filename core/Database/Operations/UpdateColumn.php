<?php
namespace Rivulet\Database\Operations;

class UpdateColumn
{
    public static function execute(string $table, string $column, string $definition)
    {
        return "ALTER TABLE {$table} MODIFY COLUMN {$column} {$definition}";
    }
}
