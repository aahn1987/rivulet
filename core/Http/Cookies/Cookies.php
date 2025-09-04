<?php
namespace Rivulet\Http\Cookies;

class Cookies
{
    private array $config;
    private array $queued = [];

    public function __construct(array $config = [])
    {
        $this->config = $config ?: config('cookies', []);
    }

    public function set(string $name, $value, array $options = []): void
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        $value = is_array($value) ? json_encode($value) : $value;

        setcookie(
            $name,
            $value,
            $options['lifetime'] ?? 0,
            $options['path'] ?? '/',
            $options['domain'] ?? '',
            $options['secure'] ?? false,
            $options['httponly'] ?? true
        );

        if (($options['samesite'] ?? null) && PHP_VERSION_ID >= 70300) {
            setcookie(
                $name,
                $value,
                [
                    'expires'  => $options['lifetime'] ?? 0,
                    'path'     => $options['path'] ?? '/',
                    'domain'   => $options['domain'] ?? '',
                    'secure'   => $options['secure'] ?? false,
                    'httponly' => $options['httponly'] ?? true,
                    'samesite' => $options['samesite'] ?? 'Lax',
                ]
            );
        }
    }

    public function get(string $name, $default = null)
    {
        return $_COOKIE[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public function forget(string $name): void
    {
        $this->set($name, '', ['lifetime' => time() - 3600]);
    }

    public function queue(string $name, $value, array $options = []): void
    {
        $this->queued[$name] = [
            'value'   => $value,
            'options' => array_merge($this->getDefaultOptions(), $options),
        ];
    }

    public function expire(string $name, int $minutes = 0): void
    {
        $this->set($name, $this->get($name), ['lifetime' => $minutes * 60]);
    }

    public function flushQueued(): void
    {
        foreach ($this->queued as $name => $cookie) {
            $this->set($name, $cookie['value'], $cookie['options']);
        }

        $this->queued = [];
    }

    private function getDefaultOptions(): array
    {
        return [
            'lifetime' => $this->config['lifetime'] ?? 0,
            'path'     => $this->config['path'] ?? '/',
            'domain'   => $this->config['domain'] ?? '',
            'secure'   => $this->config['secure'] ?? false,
            'httponly' => $this->config['http_only'] ?? true,
            'samesite' => $this->config['same_site'] ?? 'Lax',
        ];
    }
}
