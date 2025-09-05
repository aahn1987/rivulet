<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class CacheClearCommand extends Command
{
    protected string $name        = 'cache:clear';
    protected string $description = 'Clear application cache';

    public function execute(array $args): void
    {
        $this->info('Clearing cache...');

        $cachePath = storageLocation('cache');

        if (is_dir($cachePath)) {
            $this->deleteDirectory($cachePath);
            mkdir($cachePath, 0755, true);
        }

        $this->success('Cache cleared successfully');
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
