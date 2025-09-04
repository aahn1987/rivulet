<?php
namespace Rivulet\Providers;

use Rivulet\Filesystem\Filesystem;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('filesystem', function () {
            return new Filesystem();
        });

        $this->bind('files', function () {
            return $this->make('filesystem');
        });
    }

    public function boot(): void
    {
        //
    }
}
