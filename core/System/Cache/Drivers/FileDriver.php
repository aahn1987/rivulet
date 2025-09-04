<?php
namespace Rivulet\System\Cache\Drivers;

class FileDriver
{
    private string $path;

    public function __construct(array $config = [])
    {
        $this->path = $config['path'] ?? storage_path('cache');

        if (! is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function get(string $key)
    {
        $file = $this->getFilePath($key);

        if (! file_exists($file)) {
            return null;
        }

        $contents = file_get_contents($file);
        $data     = unserialize($contents);

        if ($data['expires'] !== 0 && $data['expires'] < time()) {
            $this->forget($key);
            return null;
        }

        return $data['value'];
    }

    public function put(string $key, $value, int $seconds = 3600): bool
    {
        $file = $this->getFilePath($key);
        $data = [
            'value'   => $value,
            'expires' => time() + $seconds,
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function forget(string $key): bool
    {
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }

    public function forever(string $key, $value): bool
    {
        return $this->put($key, $value, 0);
    }

    public function increment(string $key, int $value = 1): int | false
    {
        $current  = $this->get($key) ?: 0;
        $newValue = $current + $value;
        $this->put($key, $newValue, 3600);
        return $newValue;
    }

    public function decrement(string $key, int $value = 1): int | false
    {
        return $this->increment($key, -$value);
    }

    public function flush(): bool
    {
        $files = glob($this->path . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    private function getFilePath(string $key): string
    {
        return $this->path . '/' . md5($key) . '.cache';
    }
}
