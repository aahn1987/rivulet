<?php
namespace Rivulet\Database\Relations;

class BelongsTo
{
    private string $related;
    private string $foreignKey;
    private string $ownerKey;

    public function __construct(string $related, string $foreignKey, string $ownerKey)
    {
        $this->related    = $related;
        $this->foreignKey = $foreignKey;
        $this->ownerKey   = $ownerKey;
    }

    public function getRelated(): string
    {
        return $this->related;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getOwnerKey(): string
    {
        return $this->ownerKey;
    }
}
