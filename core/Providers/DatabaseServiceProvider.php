<?php
namespace Rivulet\Providers;

use Rivulet\Database\Connection;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('db', function () {
            $defaultConnection = config('database.default');
            $config            = config("database.connections.{$defaultConnection}");
            return Connection::get($config, $defaultConnection);
        });

        $this->bind('db.connection', function () {
            return $this->make('db');
        });
    }

    public function boot(): void
    {
        //
    }
}
