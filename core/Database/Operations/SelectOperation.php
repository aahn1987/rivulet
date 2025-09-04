<?php
namespace Rivulet\Database\Operations;

class SelectOperation
{
    public static function execute($query, array $bindings = [])
    {
        $stmt = $query->prepare();
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }
}
