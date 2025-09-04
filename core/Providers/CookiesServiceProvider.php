<?php
namespace Rivulet\Providers;

use Rivulet\Http\Cookies\Cookies;

class CookiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('cookies', function () {
            return new Cookies();
        });

        $this->bind('cookie', function () {
            return $this->make('cookies');
        });
    }

    public function boot(): void
    {
        $this->configureCookies();
    }

    protected function configureCookies(): void
    {
        $config = [
            'path'      => config('cookies.path', '/'),
            'domain'    => config('cookies.domain'),
            'secure'    => config('cookies.secure', false),
            'http_only' => config('cookies.http_only', true),
            'same_site' => config('cookies.same_site', 'lax'),
        ];

        $cookies = $this->make('cookies');
        $cookies->setConfig($config);
    }
}
