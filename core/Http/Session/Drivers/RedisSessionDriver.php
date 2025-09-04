<?php
namespace Rivulet\Http\Session\Drivers;

use Rivulet\Http\Session\SessionDriverInterface;

class RedisSessionDriver implements SessionDriverInterface
{
    private \Redis $redis;
    private string $id;
    private string $prefix;

    public function __construct(array $config)
    {
        $this->redis = new \Redis();
        $this->redis->connect(
            $config['host'] ?? '127.0.0.1',
            $config['port'] ?? 6379
        );

        if (isset($config['password'])) {
            $this->redis->auth($config['password']);
        }

        if (isset($config['db'])) {
            $this->redis->select($config['db']);
        }

        $this->prefix = $config['prefix'] ?? 'rivulet_session:';
    }

    public function start(): void
    {
        // Redis doesn't need explicit start
    }

    public function get(string $key, $default = null)
    {
        $data = $this->redis->get($this->prefix . $this->id);
        if ($data === false) {
            return $default;
        }

        $decoded = json_decode($data, true);
        return $decoded[$key] ?? $default;
    }

    public function put(string $key, $value): void
    {
        $data       = $this->all();
        $data[$key] = $value;
        $this->redis->set($this->prefix . $this->id, json_encode($data));
    }

    public function forget(string $key): void
    {
        $data = $this->all();
        unset($data[$key]);
        $this->redis->set($this->prefix . $this->id, json_encode($data));
    }

    public function flush(): void
    {
        $this->redis->del($this->prefix . $this->id);
    }

    public function all(): array
    {
        $data = $this->redis->get($this->prefix . $this->id);
        return $data === false ? [] : json_decode($data, true) ?: [];
    }

    public function has(string $key): bool
    {
        $data = $this->all();
        return isset($data[$key]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function regenerate(string $newId): void
    {
        $oldKey = $this->prefix . $this->id;
        $newKey = $this->prefix . $newId;

        $data = $this->redis->get($oldKey);
        if ($data !== false) {
            $this->redis->set($newKey, $data);
            $this->redis->del($oldKey);
        }

        $this->id = $newId;
    }
}
