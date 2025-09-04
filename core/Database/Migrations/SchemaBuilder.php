<?php
namespace Rivulet\Database\Migrations;

use Closure;
use Rivulet\Database\Connection;

class SchemaBuilder
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function createTable(string $table, Closure $callback): void
    {
        $builder = new ColumnBuilder();
        $callback($builder);

        $sql = "CREATE TABLE {$table} (" . $builder->getSql() . ")";
        $this->connection->pdo()->exec($sql);
    }

    public function alterTable(string $table, Closure $callback): void
    {
        $builder = new ColumnBuilder();
        $callback($builder);

        $sql = "ALTER TABLE {$table} " . $builder->getSql();
        $this->connection->pdo()->exec($sql);
    }

    public function dropTable(string $table): void
    {
        $this->connection->pdo()->exec("DROP TABLE IF EXISTS {$table}");
    }
}
