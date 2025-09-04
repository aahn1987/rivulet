<?php
namespace Rivulet\Database\Operations;

class DropTable
{
    public static function execute(string $table)
    {
        return "DROP TABLE IF EXISTS {$table}";
    }
}
