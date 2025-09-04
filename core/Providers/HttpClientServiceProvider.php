<?php
namespace Rivulet\Providers;

use Rivulet\Http\Client\Client;

class HttpClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('http.client', function () {
            return new Client();
        });

        $this->bind('http', function () {
            return $this->make('http.client');
        });
    }

    public function boot(): void
    {
        //
    }
}
