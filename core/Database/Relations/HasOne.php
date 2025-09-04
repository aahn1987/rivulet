<?php
namespace Rivulet\Database\Relations;

class HasOne
{
    private string $related;
    private string $foreignKey;
    private string $localKey;

    public function __construct(string $related, string $foreignKey, string $localKey)
    {
        $this->related    = $related;
        $this->foreignKey = $foreignKey;
        $this->localKey   = $localKey;
    }

    public function getRelated(): string
    {
        return $this->related;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getLocalKey(): string
    {
        return $this->localKey;
    }
}
