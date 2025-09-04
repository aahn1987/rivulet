<?php
namespace Rivulet\Filesystem\Operations;

class Zip
{
    public static function execute(string $source, string $destination): bool
    {
        if (! extension_loaded('zip')) {
            throw new \RuntimeException('Zip extension is not available');
        }

        $zip = new \ZipArchive();

        if ($zip->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $source = realpath($source);

        if (is_dir($source)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (! $file->isDir()) {
                    $filePath     = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($source) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }
        } elseif (is_file($source)) {
            $zip->addFile($source, basename($source));
        }

        return $zip->close();
    }
}
