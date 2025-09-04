<?php
namespace Rivulet\Providers;

use Rivulet\Views\Engine;
use Rivulet\Views\View;

class ViewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bind('view', function () {
            return new View();
        });

        $this->bind('view.engine', function () {
            return new Engine();
        });
    }

    public function boot(): void
    {
        $this->shareViewData();
    }

    protected function shareViewData(): void
    {
        $view = $this->make('view');
        $view->share('app', [
            'name' => config('app.name'),
            'url'  => config('app.url'),
            'env'  => config('app.env'),
        ]);
    }
}
