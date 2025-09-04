<?php
namespace Rivulet\Filesystem\Operations;

class CreateFile
{
    public static function execute(string $path, string $contents, bool $lock = false): bool
    {
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $flags = $lock ? LOCK_EX : 0;

        return file_put_contents($path, $contents, $flags) !== false;
    }
}
