<?php
namespace Rivulet\Database\Migrations;

use Closure;
use Rivulet\Database\Connection;

abstract class Migration
{
    protected Connection $connection;
    protected string $connectionName = 'default';

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    abstract public function up(): void;
    abstract public function down(): void;

    protected function executeSchema(Closure $callback): void
    {
        $builder = new SchemaBuilder($this->connection);
        $callback($builder);
    }

    protected function createTable(string $table, Closure $callback): void
    {
        $builder = new SchemaBuilder($this->connection);
        $builder->createTable($table, $callback);
    }

    protected function dropTable(string $table): void
    {
        $this->connection->pdo()->exec("DROP TABLE IF EXISTS {$table}");
    }

    protected function alterTable(string $table, Closure $callback): void
    {
        $builder = new SchemaBuilder($this->connection);
        $builder->alterTable($table, $callback);
    }
}
