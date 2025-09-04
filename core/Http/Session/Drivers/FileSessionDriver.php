<?php
namespace Rivulet\Http\Session\Drivers;

use Rivulet\Http\Session\SessionDriverInterface;

class FileSessionDriver implements SessionDriverInterface
{
    private string $path;
    private string $id;
    private array $data = [];

    public function __construct(array $config)
    {
        $this->path = $config['files'] ?? storage_path('sessions');

        if (! is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function start(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        $file = $this->getFilePath();

        if (file_exists($file)) {
            $content    = file_get_contents($file);
            $this->data = unserialize($content) ?: [];
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function put(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->saveData();
    }

    public function forget(string $key): void
    {
        unset($this->data[$key]);
        $this->saveData();
    }

    public function flush(): void
    {
        $this->data = [];
        $this->saveData();
    }

    public function all(): array
    {
        return $this->data;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
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
        $oldFile  = $this->getFilePath();
        $this->id = $newId;
        $newFile  = $this->getFilePath();

        if (file_exists($oldFile)) {
            rename($oldFile, $newFile);
        }
    }

    private function saveData(): void
    {
        $file = $this->getFilePath();
        file_put_contents($file, serialize($this->data));
    }

    private function getFilePath(): string
    {
        return $this->path . '/sess_' . $this->id;
    }
}
