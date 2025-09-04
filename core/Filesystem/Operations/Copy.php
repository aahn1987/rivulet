<?php
namespace Rivulet\Filesystem\Operations;

class Copy
{
    public static function execute(string $from, string $to): bool
    {
        if (! file_exists($from)) {
            return false;
        }

        $dir = dirname($to);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return copy($from, $to);
    }
}
