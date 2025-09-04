<?php
namespace Rivulet\Filesystem\Operations;

class DeleteFile
{
    public static function execute(string $path): bool
    {
        if (! file_exists($path)) {
            return false;
        }

        return unlink($path);
    }
}
