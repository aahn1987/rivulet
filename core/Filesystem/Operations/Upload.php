<?php
namespace Rivulet\Filesystem\Operations;

class Upload
{
    public static function execute(array $file, string $destination, string $filename = null): bool
    {
        if (! isset($file['tmp_name']) || ! isset($file['error'])) {
            return false;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename        = $filename ?? $file['name'];
        $destinationPath = rtrim($destination, '/') . '/' . $filename;

        return move_uploaded_file($file['tmp_name'], $destinationPath);
    }
}
