<?php
namespace Rivulet\Filesystem\Operations;

class CreateDirectory
{
    public static function execute(string $path, int $mode = 0755, bool $recursive = false, bool $force = false): bool
    {
        if ($force && is_dir($path)) {
            return true;
        }

        return mkdir($path, $mode, $recursive);
    }
}
