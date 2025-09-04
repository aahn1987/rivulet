<?php
namespace Rivulet\Validation\Rules;

class Exists
{
    private string $table;
    private ?string $column;

    public function __construct(string $params)
    {
        $parts        = explode(',', $params);
        $this->table  = $parts[0];
        $this->column = $parts[1] ?? null;
    }

    public function passes($value): bool
    {
        $modelClass = '\\App\\Models\\' . ucfirst($this->table);

        if (! class_exists($modelClass)) {
            return false;
        }

        $model = new $modelClass();
        $query = $model->newQuery();

        $column = $this->column ?? 'id';

        return $query->where($column, $value)->exists();
    }

    public function message(): string
    {
        return 'The selected value is invalid.';
    }
}
