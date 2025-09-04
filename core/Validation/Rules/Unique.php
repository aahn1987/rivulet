<?php
namespace Rivulet\Validation\Rules;

class Unique
{
    private string $table;
    private ?string $column;
    private ?string $ignore;

    public function __construct(string $params)
    {
        $parts        = explode(',', $params);
        $this->table  = $parts[0];
        $this->column = $parts[1] ?? null;
        $this->ignore = $parts[2] ?? null;
    }

    public function passes($value): bool
    {
        $modelClass = '\\App\\Models\\' . ucfirst($this->table);

        if (! class_exists($modelClass)) {
            return true;
        }

        $model = new $modelClass();
        $query = $model->newQuery();

        $column = $this->column ?? 'email';

        $query->where($column, $value);

        if ($this->ignore) {
            $query->where('id', '!=', $this->ignore);
        }

        return ! $query->exists();
    }

    public function message(): string
    {
        return 'This value has already been taken.';
    }
}
