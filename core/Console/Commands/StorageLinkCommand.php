<?php
namespace Rivulet\Console\Commands;

use Rivulet\Console\Command;

class StorageLinkCommand extends Command
{
    protected string $name        = 'storage:link';
    protected string $description = 'Create storage symbolic links';

    public function execute(array $args): void
    {
        $this->info('Creating storage symbolic links...');

        $publicPath  = public_path();
        $storagePath = storage_path();

        $links = [
            'storage' => $storagePath . '/uploads',
            'uploads' => $storagePath . '/uploads',
        ];

        foreach ($links as $link => $target) {
            $linkPath = $publicPath . '/' . $link;

            if (file_exists($target)) {
                if (is_link($linkPath)) {
                    unlink($linkPath);
                }

                if (symlink($target, $linkPath)) {
                    $this->success("Created symlink: {$linkPath} -> {$target}");
                } else {
                    $this->error("Failed to create symlink: {$linkPath}");
                }
            } else {
                $this->warning("Target directory not found: {$target}");
            }
        }

        $this->success('Storage links created successfully');
    }
}
