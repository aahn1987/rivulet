<?php

use Rivulet\Routing\Router;

$router = new Router();

$router->get('/', function () {
    return [
        'message' => 'Welcome to Rivulet API Framework',
        'version' => '1.0.0',
        'status'  => 'running',
    ];
});

$router->group(['prefix' => 'api'], function ($router) {
    $router->group(['prefix' => 'v1'], function ($router) {
        // User routes
        $router->resource('users', 'App\Controllers\UsersController');

        // Additional routes
        $router->get('health', function () {
            return ['status' => 'healthy'];
        });
    });
});

// Version with header
$router->version('v1')->group(['prefix' => 'api'], function ($router) {
    $router->get('test', function () {
        return ['version' => 'v1'];
    });
});

$router->version('v2')->group(['prefix' => 'api'], function ($router) {
    $router->get('test', function () {
        return ['version' => 'v2'];
    });
});
