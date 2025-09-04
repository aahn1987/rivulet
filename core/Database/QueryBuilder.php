<?php
namespace Rivulet\Database;

use PDO;

class QueryBuilder
{
    private Connection $connection;
    private string $table;
    private array $wheres    = [];
    private array $selects   = ['*'];
    private array $joins     = [];
    private array $orders    = [];
    private ?int $limit      = null;
    private ?int $offset     = null;
    private array $bindings  = [];
    private array $relations = [];

    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table      = $table;
    }

    public function select(array $columns = ['*']): self
    {
        $this->selects = $columns;
        return $this;
    }

    public function where(string $column, $operator = null, $value = null): self
    {
        if ($value === null && $operator !== null) {
            $value    = $operator;
            $operator = '=';
        }

        $this->wheres[]   = compact('column', 'operator', 'value');
        $this->bindings[] = $value;

        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $this->wheres[] = [
            'column'   => $column,
            'operator' => 'IN',
            'value'    => $values,
        ];

        $this->bindings = array_merge($this->bindings, $values);

        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = [
            'column'   => $column,
            'operator' => 'IS NULL',
            'value'    => null,
        ];

        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = [
            'column'   => $column,
            'operator' => 'IS NOT NULL',
            'value'    => null,
        ];

        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self
    {
        $this->joins[] = [
            'table'    => $table,
            'first'    => $first,
            'operator' => $operator,
            'second'   => $second,
            'type'     => $type,
        ];

        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = compact('column', 'direction');
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function with(string ...$relations): self
    {
        $this->relations = array_merge($this->relations, $relations);
        return $this;
    }

    public function get(): array
    {
        $sql  = $this->buildSelectQuery();
        $stmt = $this->connection->pdo()->prepare($sql);
        $stmt->execute($this->bindings);

        $results = $stmt->fetchAll();

        if (! empty($this->relations)) {
            $results = $this->loadRelations($results);
        }

        return $results;
    }

    public function first()
    {
        $this->limit(1);
        $results = $this->get();

        return $results[0] ?? null;
    }

    public function count(): int
    {
        $sql  = $this->buildCountQuery();
        $stmt = $this->connection->pdo()->prepare($sql);
        $stmt->execute($this->bindings);

        return (int) $stmt->fetchColumn();
    }

    public function insert(array $data): bool
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql  = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->connection->pdo()->prepare($sql);

        return $stmt->execute(array_values($data));
    }

    public function update(array $data): int
    {
        $set      = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            $set[]      = "{$column} = ?";
            $bindings[] = $value;
        }

        $bindings = array_merge($bindings, $this->bindings);

        $sql  = "UPDATE {$this->table} SET " . implode(', ', $set) . $this->buildWhereClause();
        $stmt = $this->connection->pdo()->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->rowCount();
    }

    public function delete(): int
    {
        $sql  = "DELETE FROM {$this->table}" . $this->buildWhereClause();
        $stmt = $this->connection->pdo()->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->rowCount();
    }

    private function buildSelectQuery(): string
    {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";

        if (! empty($this->joins)) {
            foreach ($this->joins as $join) {
                $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
            }
        }

        $sql .= $this->buildWhereClause();

        if (! empty($this->orders)) {
            $sql .= " ORDER BY " . implode(', ', array_map(fn($order) => "{$order['column']} {$order['direction']}", $this->orders));
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    private function buildCountQuery(): string
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";

        if (! empty($this->joins)) {
            foreach ($this->joins as $join) {
                $sql .= " {$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
            }
        }

        $sql .= $this->buildWhereClause();

        return $sql;
    }

    private function buildWhereClause(): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        $conditions = [];

        foreach ($this->wheres as $where) {
            if ($where['operator'] === 'IN') {
                $placeholders = implode(', ', array_fill(0, count($where['value']), '?'));
                $conditions[] = "{$where['column']} IN ({$placeholders})";
            } elseif ($where['operator'] === 'IS NULL' || $where['operator'] === 'IS NOT NULL') {
                $conditions[] = "{$where['column']} {$where['operator']}";
            } else {
                $conditions[] = "{$where['column']} {$where['operator']} ?";
            }
        }

        return " WHERE " . implode(' AND ', $conditions);
    }

    private function loadRelations(array $results): array
    {
        return $results;
    }
}
