<?php
namespace Rivulet\System\Cache;

class Cache
{
    private string $driver = 'file';
    private string $prefix = 'rivulet';
    private array $config  = [];

    public function __construct(string $driver = null, array $config = [])
    {
        $this->driver = $driver ?? env('CACHE_DRIVER', 'file');
        $this->config = $config;
        $this->prefix = env('CACHE_PREFIX', 'rivulet');
    }

    public function get(string $key, $default = null)
    {
        $value = $this->getDriver()->get($this->key($key));
        return $value ?? $default;
    }

    public function put(string $key, $value, int $seconds = 3600): bool
    {
        return $this->getDriver()->put($this->key($key), $value, $seconds);
    }

    public function has(string $key): bool
    {
        return $this->getDriver()->has($this->key($key));
    }

    public function forget(string $key): bool
    {
        return $this->getDriver()->forget($this->key($key));
    }

    public function forever(string $key, $value): bool
    {
        return $this->getDriver()->forever($this->key($key), $value);
    }

    public function increment(string $key, int $value = 1): int | false
    {
        return $this->getDriver()->increment($this->key($key), $value);
    }

    public function decrement(string $key, int $value = 1): int | false
    {
        return $this->getDriver()->decrement($this->key($key), $value);
    }

    public function remember(string $key, int $seconds, callable $callback)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->put($key, $value, $seconds);

        return $value;
    }

    public function rememberForever(string $key, callable $callback)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->forever($key, $value);

        return $value;
    }

    public function flush(): bool
    {
        return $this->getDriver()->flush();
    }

    private function getDriver()
    {
        switch ($this->driver) {
            case 'redis':
                return new \Rivulet\System\Cache\Drivers\RedisDriver($this->config);
            case 'file':
            default:
                return new \Rivulet\System\Cache\Drivers\FileDriver($this->config);
        }
    }

    private function key(string $key): string
    {
        return $this->prefix . ':' . $key;
    }

    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setDriver(string $driver): self
    {
        $this->driver = $driver;
        return $this;
    }
}
