<?php
namespace Rivulet\Filesystem;

use Rivulet\Filesystem\Operations\Copy;
use Rivulet\Filesystem\Operations\CreateDirectory;
use Rivulet\Filesystem\Operations\CreateFile;
use Rivulet\Filesystem\Operations\DeleteDirectory;
use Rivulet\Filesystem\Operations\DeleteFile;
use Rivulet\Filesystem\Operations\Download;
use Rivulet\Filesystem\Operations\Extract;
use Rivulet\Filesystem\Operations\Move;
use Rivulet\Filesystem\Operations\Rename;
use Rivulet\Filesystem\Operations\Upload;
use Rivulet\Filesystem\Operations\Zip;

class Filesystem
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function missing(string $path): bool
    {
        return ! file_exists($path);
    }

    public function get(string $path): string
    {
        if (! $this->exists($path)) {
            throw new \RuntimeException("File does not exist: {$path}");
        }

        return file_get_contents($path);
    }

    public function put(string $path, string $contents, bool $lock = false): bool
    {
        return CreateFile::execute($path, $contents, $lock);
    }

    public function append(string $path, string $data): bool
    {
        return file_put_contents($path, $data, FILE_APPEND | LOCK_EX) !== false;
    }

    public function prepend(string $path, string $data): bool
    {
        if ($this->exists($path)) {
            return $this->put($path, $data . $this->get($path));
        }

        return $this->put($path, $data);
    }

    public function delete(string | array $paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        foreach ($paths as $path) {
            if (! $this->deleteOne($path)) {
                return false;
            }
        }

        return true;
    }

    private function deleteOne(string $path): bool
    {
        return DeleteFile::execute($path);
    }

    public function move(string $from, string $to): bool
    {
        return Move::execute($from, $to);
    }

    public function copy(string $from, string $to): bool
    {
        return Copy::execute($from, $to);
    }

    public function rename(string $from, string $to): bool
    {
        return Rename::execute($from, $to);
    }

    public function size(string $path): int
    {
        return filesize($path);
    }

    public function lastModified(string $path): int
    {
        return filemtime($path);
    }

    public function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    public function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    public function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    public function glob(string $pattern, int $flags = 0): array
    {
        return glob($pattern, $flags) ?: [];
    }

    public function files(string $directory, bool $hidden = false): array
    {
        $files = [];

        foreach ($this->glob($directory . '/*') as $file) {
            if ($this->isFile($file)) {
                $files[] = $file;
            }
        }

        if ($hidden) {
            foreach ($this->glob($directory . '/.*') as $file) {
                if ($this->isFile($file) && basename($file) !== '.' && basename($file) !== '..') {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    public function allFiles(string $directory, bool $hidden = false): array
    {
        $files = [];

        foreach ($this->glob($directory . '/*') as $file) {
            if ($this->isFile($file)) {
                $files[] = $file;
            } elseif ($this->isDirectory($file)) {
                $files = array_merge($files, $this->allFiles($file, $hidden));
            }
        }

        if ($hidden) {
            foreach ($this->glob($directory . '/.*') as $file) {
                if ($this->isFile($file) && basename($file) !== '.' && basename($file) !== '..') {
                    $files[] = $file;
                } elseif ($this->isDirectory($file) && basename($file) !== '.' && basename($file) !== '..') {
                    $files = array_merge($files, $this->allFiles($file, $hidden));
                }
            }
        }

        return $files;
    }

    public function directories(string $directory): array
    {
        $directories = [];

        foreach ($this->glob($directory . '/*') as $item) {
            if ($this->isDirectory($item)) {
                $directories[] = $item;
            }
        }

        return $directories;
    }

    public function makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false): bool
    {
        return CreateDirectory::execute($path, $mode, $recursive, $force);
    }

    public function deleteDirectory(string $directory, bool $preserve = false): bool
    {
        return DeleteDirectory::execute($directory, $preserve);
    }

    public function cleanDirectory(string $directory): bool
    {
        if (! $this->isDirectory($directory)) {
            return false;
        }

        $items = $this->glob($directory . '/*');

        foreach ($items as $item) {
            if ($this->isDirectory($item)) {
                $this->deleteDirectory($item);
            } else {
                $this->delete($item);
            }
        }

        return true;
    }

    public function upload(array $file, string $destination, string $filename = null): bool
    {
        return Upload::execute($file, $destination, $filename);
    }

    public function download(string $url, string $destination): bool
    {
        return Download::execute($url, $destination);
    }

    public function zip(string $source, string $destination): bool
    {
        return Zip::execute($source, $destination);
    }

    public function extract(string $zipFile, string $destination): bool
    {
        return Extract::execute($zipFile, $destination);
    }

    public function url(string $path): string
    {
        return asset($path);
    }

    public function path(string $path): string
    {
        return publicLocation($path);
    }

    public function temporaryUrl(string $path, int $expiration): string
    {
        $token   = strRandom(40);
        $expires = time() + $expiration;

        cache()->put("temp_url:{$token}", $path, $expiration);

        return asset($path) . '?token=' . $token . '&expires=' . $expires;
    }

    public function mimeType(string $path): string
    {
        return mime_content_type($path) ?: 'application/octet-stream';
    }

    public function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    public function name(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public function basename(string $path): string
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    public function dirname(string $path): string
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    public function type(string $path): string
    {
        return filetype($path);
    }

    public function permissions(string $path): int
    {
        return fileperms($path) & 0777;
    }

    public function chmod(string $path, int $mode): bool
    {
        return chmod($path, $mode);
    }
}
