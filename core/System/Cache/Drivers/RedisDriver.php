<?php
namespace Rivulet\System\Cache\Drivers;

class RedisDriver
{
    private \Redis $redis;

    public function __construct(array $config = [])
    {
        $this->redis = new \Redis();

        $host     = $config['host'] ?? '127.0.0.1';
        $port     = $config['port'] ?? 6379;
        $password = $config['password'] ?? null;

        $this->redis->connect($host, $port);

        if ($password) {
            $this->redis->auth($password);
        }

        $db = $config['db'] ?? 0;
        $this->redis->select($db);
    }

    public function get(string $key)
    {
        $value = $this->redis->get($key);
        return $value !== false ? unserialize($value) : null;
    }

    public function put(string $key, $value, int $seconds = 3600): bool
    {
        return $this->redis->setex($key, $seconds, serialize($value));
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }

    public function forget(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }

    public function forever(string $key, $value): bool
    {
        return $this->redis->set($key, serialize($value));
    }

    public function increment(string $key, int $value = 1): int | false
    {
        return $this->redis->incrBy($key, $value);
    }

    public function decrement(string $key, int $value = 1): int | false
    {
        return $this->redis->decrBy($key, $value);
    }

    public function flush(): bool
    {
        return $this->redis->flushDB();
    }
}
