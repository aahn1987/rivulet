<?php
namespace Rivulet;

use Rivulet\Database\Connection;
use Rivulet\Database\QueryBuilder;
use Rivulet\Database\Relations\BelongsTo;
use Rivulet\Database\Relations\BelongsToMany;
use Rivulet\Database\Relations\HasMany;
use Rivulet\Database\Relations\HasOne;

abstract class Model
{
    protected string $connection = 'default';
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable    = [];
    protected array $guarded     = [];
    protected array $hidden      = [];
    protected array $casts       = [];
    protected bool $timestamps   = true;
    protected bool $softDeletes  = false;

    protected array $attributes = [];
    protected array $original   = [];
    protected bool $exists      = false;

    protected static array $relations = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    protected function isFillable(string $key): bool
    {
        if (in_array($key, $this->guarded)) {
            return false;
        }

        if (empty($this->fillable)) {
            return true;
        }

        return in_array($key, $this->fillable);
    }

    public function getConnection(): Connection
    {
        $config = config("database.connections.{$this->connection}");
        return Connection::get($config, $this->connection);
    }

    public function getTable(): string
    {
        return $this->table ?? strtolower(class_basename($this)) . 's';
    }

    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    public function getKey()
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }

    public function newQuery(): QueryBuilder
    {
        return new QueryBuilder($this->getConnection(), $this->getTable());
    }

    public static function query(): QueryBuilder
    {
        return (new static )->newQuery();
    }

    public static function find($id)
    {
        return static::query()->where('id', $id)->first();
    }

    public static function findOrFail($id)
    {
        $model = static::find($id);

        if (! $model) {
            abort(404, 'Model not found');
        }

        return $model;
    }

    public static function all(): array
    {
        return static::query()->get();
    }

    public static function where(string $column, $operator = null, $value = null): QueryBuilder
    {
        return static::query()->where($column, $operator, $value);
    }

    public static function with(string ...$relations): QueryBuilder
    {
        return static::query()->with(...$relations);
    }

    public function create(array $attributes = []): self
    {
        $model = new static($attributes);
        $model->save();

        return $model;
    }

    public function save(): bool
    {
        if ($this->exists) {
            return $this->performUpdate();
        }

        return $this->performInsert();
    }

    protected function performInsert(): bool
    {
        if ($this->timestamps) {
            $this->attributes['created_at'] = now()->format('Y-m-d H:i:s');
            $this->attributes['updated_at'] = now()->format('Y-m-d H:i:s');
        }

        $attributes = $this->getAttributesForInsert();

        $result = $this->newQuery()->insert($attributes);

        if ($result) {
            $this->exists   = true;
            $this->original = $this->attributes;

            if (! isset($this->attributes[$this->primaryKey])) {
                $this->attributes[$this->primaryKey] = $this->getConnection()->lastInsertId();
            }
        }

        return $result;
    }

    protected function performUpdate(): bool
    {
        if ($this->timestamps) {
            $this->attributes['updated_at'] = now()->format('Y-m-d H:i:s');
        }

        $dirty = $this->getDirty();

        if (empty($dirty)) {
            return true;
        }

        $result = $this->newQuery()
            ->where($this->primaryKey, $this->getKey())
            ->update($dirty);

        if ($result) {
            $this->original = $this->attributes;
        }

        return (bool) $result;
    }

    public function update(array $attributes): bool
    {
        $this->fill($attributes);
        return $this->save();
    }

    public function delete(): bool
    {
        if ($this->softDeletes) {
            $this->attributes['deleted_at'] = now()->format('Y-m-d H:i:s');
            return $this->save();
        }

        return (bool) $this->newQuery()
            ->where($this->primaryKey, $this->getKey())
            ->delete();
    }

    public function forceDelete(): bool
    {
        return (bool) $this->newQuery()
            ->where($this->primaryKey, $this->getKey())
            ->delete();
    }

    public static function destroy($ids): int
    {
        $ids = is_array($ids) ? $ids : func_get_args();

        return (new static )->newQuery()
            ->whereIn('id', $ids)
            ->delete();
    }

    public function hasOne(string $related, string $foreignKey = null, string $localKey = null): HasOne
    {
        $instance   = new $related;
        $foreignKey = $foreignKey ?? $this->getForeignKey();
        $localKey   = $localKey ?? $this->primaryKey;

        return new HasOne($related, $foreignKey, $localKey);
    }

    public function hasMany(string $related, string $foreignKey = null, string $localKey = null): HasMany
    {
        $instance   = new $related;
        $foreignKey = $foreignKey ?? $this->getForeignKey();
        $localKey   = $localKey ?? $this->primaryKey;

        return new HasMany($related, $foreignKey, $localKey);
    }

    public function belongsTo(string $related, string $foreignKey = null, string $ownerKey = null): BelongsTo
    {
        $instance   = new $related;
        $foreignKey = $foreignKey ?? $this->getForeignKey($related);
        $ownerKey   = $ownerKey ?? $instance->primaryKey;

        return new BelongsTo($related, $foreignKey, $ownerKey);
    }

    public function belongsToMany(string $related, string $table = null, string $foreignKey = null, string $relatedKey = null): BelongsToMany
    {
        $instance   = new $related;
        $table      = $table ?? $this->getPivotTableName($instance);
        $foreignKey = $foreignKey ?? $this->getForeignKey();
        $relatedKey = $relatedKey ?? $instance->getForeignKey();

        return new BelongsToMany($related, $table, $foreignKey, $relatedKey);
    }

    protected function getForeignKey(string $related = null): string
    {
        $related = $related ?? strtolower(class_basename($this));
        return strtolower(class_basename($this)) . '_id';
    }

    protected function getPivotTableName($related): string
    {
        $names = [strtolower(class_basename($this)), strtolower(class_basename($related))];
        sort($names);

        return implode('_', $names);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function setAttribute(string $key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    protected function getAttributesForInsert(): array
    {
        $attributes = [];

        foreach ($this->attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $attributes[$key] = $this->castAttribute($key, $value);
            }
        }

        return $attributes;
    }

    protected function castAttribute(string $key, $value)
    {
        if (! isset($this->casts[$key])) {
            return $value;
        }

        switch ($this->casts[$key]) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'array':
                return is_string($value) ? json_decode($value, true) : (array) $value;
            case 'json':
                return is_string($value) ? json_decode($value) : $value;
            default:
                return $value;
        }
    }

    public function getDirty(): array
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (! array_key_exists($key, $this->original) || $value !== $this->original[$key]) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    public function toArray(): array
    {
        $array = $this->attributes;

        foreach ($this->hidden as $key) {
            unset($array[$key]);
        }

        return $array;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function __isset(string $key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset(string $key)
    {
        unset($this->attributes[$key]);
    }
}
