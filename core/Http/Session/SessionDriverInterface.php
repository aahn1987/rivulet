<?php
namespace Rivulet\Http\Session;

interface SessionDriverInterface
{
    public function start(): void;
    public function get(string $key, $default = null);
    public function put(string $key, $value): void;
    public function forget(string $key): void;
    public function flush(): void;
    public function all(): array;
    public function has(string $key): bool;
    public function getId(): string;
    public function setId(string $id): void;
    public function regenerate(string $newId): void;
}
