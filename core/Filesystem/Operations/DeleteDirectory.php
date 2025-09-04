<?php
namespace Rivulet\Filesystem\Operations;

class DeleteDirectory
{
    public static function execute(string $directory, bool $preserve = false): bool
    {
        if (! is_dir($directory)) {
            return false;
        }

        $items = array_diff(scandir($directory), ['.', '..']);

        foreach ($items as $item) {
            $path = $directory . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                self::execute($path, false);
            } else {
                unlink($path);
            }
        }

        if (! $preserve) {
            return rmdir($directory);
        }

        return true;
    }
}
