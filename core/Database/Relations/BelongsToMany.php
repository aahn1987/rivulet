<?php
namespace Rivulet\Database\Relations;

class BelongsToMany
{
    private string $related;
    private string $table;
    private string $foreignKey;
    private string $relatedKey;

    public function __construct(string $related, string $table, string $foreignKey, string $relatedKey)
    {
        $this->related    = $related;
        $this->table      = $table;
        $this->foreignKey = $foreignKey;
        $this->relatedKey = $relatedKey;
    }

    public function getRelated(): string
    {
        return $this->related;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getRelatedKey(): string
    {
        return $this->relatedKey;
    }
}
