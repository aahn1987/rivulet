<?php
namespace Rivulet\Database\Migrations;

class ColumnBuilder
{
    private array $columns  = [];
    private array $commands = [];

    public function id(): self
    {
        $this->columns[] = 'id INT AUTO_INCREMENT PRIMARY KEY';
        return $this;
    }

    public function bigId(): self
    {
        $this->columns[] = 'id BIGINT AUTO_INCREMENT PRIMARY KEY';
        return $this;
    }

    public function string(string $name, int $length = 255): self
    {
        $this->columns[] = "{$name} VARCHAR({$length})";
        return $this;
    }

    public function text(string $name): self
    {
        $this->columns[] = "{$name} TEXT";
        return $this;
    }

    public function longText(string $name): self
    {
        $this->columns[] = "{$name} LONGTEXT";
        return $this;
    }

    public function integer(string $name): self
    {
        $this->columns[] = "{$name} INT";
        return $this;
    }

    public function bigInteger(string $name): self
    {
        $this->columns[] = "{$name} BIGINT";
        return $this;
    }

    public function smallInteger(string $name): self
    {
        $this->columns[] = "{$name} SMALLINT";
        return $this;
    }

    public function float(string $name, int $precision = 8, int $scale = 2): self
    {
        $this->columns[] = "{$name} FLOAT({$precision}, {$scale})";
        return $this;
    }

    public function decimal(string $name, int $precision = 8, int $scale = 2): self
    {
        $this->columns[] = "{$name} DECIMAL({$precision}, {$scale})";
        return $this;
    }

    public function boolean(string $name): self
    {
        $this->columns[] = "{$name} BOOLEAN";
        return $this;
    }

    public function date(string $name): self
    {
        $this->columns[] = "{$name} DATE";
        return $this;
    }

    public function dateTime(string $name): self
    {
        $this->columns[] = "{$name} DATETIME";
        return $this;
    }

    public function time(string $name): self
    {
        $this->columns[] = "{$name} TIME";
        return $this;
    }

    public function timestamp(string $name): self
    {
        $this->columns[] = "{$name} TIMESTAMP";
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns[] = 'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP';
        $this->columns[] = 'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
        return $this;
    }

    public function softDeletes(): self
    {
        $this->columns[] = 'deleted_at TIMESTAMP NULL';
        return $this;
    }

    public function unique(): self
    {
        $lastKey = array_key_last($this->columns);
        $this->columns[$lastKey] .= ' UNIQUE';
        return $this;
    }

    public function nullable(): self
    {
        $lastKey                 = array_key_last($this->columns);
        $this->columns[$lastKey] = str_replace('NOT NULL', '', $this->columns[$lastKey]);
        return $this;
    }

    public function notNull(): self
    {
        $lastKey = array_key_last($this->columns);
        if (! str_contains($this->columns[$lastKey], 'NOT NULL')) {
            $this->columns[$lastKey] .= ' NOT NULL';
        }
        return $this;
    }

    public function default($value): self
    {
        $lastKey = array_key_last($this->columns);
        $this->columns[$lastKey] .= " DEFAULT " . (is_string($value) ? "'{$value}'" : $value);
        return $this;
    }

    public function index(string $name = null): self
    {
        $this->commands[] = 'INDEX ' . ($name ?: 'idx_' . uniqid());
        return $this;
    }

    public function dropColumn(string $name): self
    {
        $this->commands[] = "DROP COLUMN {$name}";
        return $this;
    }

    public function renameColumn(string $from, string $to): self
    {
        $this->commands[] = "CHANGE {$from} {$to}";
        return $this;
    }

    public function modifyColumn(string $name, string $type): self
    {
        $this->commands[] = "MODIFY {$name} {$type}";
        return $this;
    }

    public function addColumn(string $name, string $type): self
    {
        $this->commands[] = "ADD {$name} {$type}";
        return $this;
    }

    public function getSql(): string
    {
        if (! empty($this->commands)) {
            return implode(', ', $this->commands);
        }

        return implode(', ', $this->columns);
    }
}
