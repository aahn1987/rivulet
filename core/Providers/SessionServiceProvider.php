<?php
namespace Rivulet\Providers;

use Rivulet\Http\Session\Session;

class SessionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('session', function () {
            return new Session();
        });

        $this->bind('session.store', function () {
            return $this->make('session');
        });
    }

    public function boot(): void
    {
        $this->configureSession();
    }

    protected function configureSession(): void
    {
        $config = [
            'driver'    => config('session.driver', 'file'),
            'lifetime'  => config('session.lifetime', 120),
            'path'      => config('session.path', '/'),
            'domain'    => config('session.domain'),
            'secure'    => config('session.secure', false),
            'http_only' => config('session.http_only', true),
            'files'     => config('session.files', storageLocation('sessions')),
        ];

        $session = $this->make('session');
        $session->setConfig($config);
    }
}
