<?php
namespace Rivulet\Filesystem\Operations;

class Extract
{
    public static function execute(string $zipFile, string $destination): bool
    {
        if (! extension_loaded('zip')) {
            throw new \RuntimeException('Zip extension is not available');
        }

        if (! file_exists($zipFile)) {
            return false;
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipFile) !== true) {
            return false;
        }

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $result = $zip->extractTo($destination);
        $zip->close();

        return $result;
    }
}
