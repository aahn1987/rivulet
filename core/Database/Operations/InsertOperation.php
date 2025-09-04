<?php
namespace Rivulet\Database\Operations;

class InsertOperation
{
    public static function execute($query, array $bindings = [])
    {
        $stmt = $query->prepare();
        $stmt->execute($bindings);
        return $stmt;
    }
}
