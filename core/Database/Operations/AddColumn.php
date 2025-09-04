<?php
namespace Rivulet\Database\Operations;

class AddColumn
{
    public static function execute(string $table, string $column, string $definition)
    {
        return "ALTER TABLE {$table} ADD COLUMN {$column} {$definition}";
    }
}
