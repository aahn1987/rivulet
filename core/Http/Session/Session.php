<?php
namespace Rivulet\Http\Session;

class Session
{
    private array $config;
    private string $driver;
    private SessionDriverInterface $driverInstance;
    private bool $started = false;

    public function __construct(array $config = [])
    {
        $this->config         = $config ?: config('session', []);
        $this->driver         = $this->config['driver'] ?? 'file';
        $this->driverInstance = $this->createDriver();
    }

    private function createDriver(): SessionDriverInterface
    {
        switch ($this->driver) {
            case 'file':
                return new \Rivulet\Http\Session\Drivers\FileSessionDriver($this->config);
            case 'redis':
                return new \Rivulet\Http\Session\Drivers\RedisSessionDriver($this->config);
            case 'database':
                return new \Rivulet\Http\Session\Drivers\DatabaseSessionDriver($this->config);
            default:
                return new \Rivulet\Http\Session\Drivers\FileSessionDriver($this->config);
        }
    }

    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        $sessionId = $_COOKIE[$this->getName()] ?? null;

        if (! $sessionId) {
            $sessionId = $this->generateSessionId();
            $this->setCookie($sessionId);
        }

        $this->driverInstance->setId($sessionId);
        $this->driverInstance->start();
        $this->started = true;

        return true;
    }

    public function get(string $key, $default = null)
    {
        $this->start();
        return $this->driverInstance->get($key, $default);
    }

    public function put(string $key, $value): void
    {
        $this->start();
        $this->driverInstance->put($key, $value);
    }

    public function push(string $key, $value): void
    {
        $this->start();
        $array   = $this->get($key, []);
        $array[] = $value;
        $this->put($key, $array);
    }

    public function forget(string $key): void
    {
        $this->start();
        $this->driverInstance->forget($key);
    }

    public function flush(): void
    {
        $this->start();
        $this->driverInstance->flush();
    }

    public function all(): array
    {
        $this->start();
        return $this->driverInstance->all();
    }

    public function has(string $key): bool
    {
        $this->start();
        return $this->driverInstance->has($key);
    }

    public function regenerate(): bool
    {
        $this->start();
        $oldId = $this->getId();
        $newId = $this->generateSessionId();

        $this->driverInstance->regenerate($newId);
        $this->setCookie($newId);

        return true;
    }

    public function invalidate(): bool
    {
        $this->flush();
        return $this->regenerate();
    }

    public function getId(): string
    {
        return $this->driverInstance->getId();
    }

    public function setId(string $id): void
    {
        $this->driverInstance->setId($id);
        $this->setCookie($id);
    }

    public function getName(): string
    {
        return $this->config['name'] ?? 'rivulet_session';
    }

    private function generateSessionId(): string
    {
        return strRandom(40);
    }

    private function setCookie(string $sessionId): void
    {
        $cookie = new \Rivulet\Http\Cookies\Cookies();
        $cookie->set($this->getName(), $sessionId, [
            'lifetime' => $this->config['lifetime'] ?? 120 * 60,
            'path'     => $this->config['path'] ?? '/',
            'domain'   => $this->config['domain'] ?? '',
            'secure'   => $this->config['secure'] ?? false,
            'httponly' => $this->config['http_only'] ?? true,
            'samesite' => $this->config['same_site'] ?? 'lax',
        ]);
    }

    public function __get(string $key)
    {
        return $this->get($key);
    }

    public function __set(string $key, $value)
    {
        $this->put($key, $value);
    }

    public function __isset(string $key)
    {
        return $this->has($key);
    }

    public function __unset(string $key)
    {
        $this->forget($key);
    }
}
