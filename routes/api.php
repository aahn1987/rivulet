<?php

// Welcome route
GetRoute('/', function () {
    return [
        'message' => 'Welcome to Rivulet API Framework',
        'version' => '1.0.0',
        'status'  => 'running',
    ];
});

// Standard API routes without versioning
Prefix('api', function () {
    Prefix('v1', function () {
        RouteCollection('users', 'App\Controllers\UsersController');

        GetRoute('health', function () {
            return ['status' => 'healthy'];
        });
    });
});

// Version-based routing using header versioning
VersionedPrefix('v1', 'api', function () {
    GetRoute('test', function () {
        return ['version' => 'v1', 'method' => 'header-based'];
    });

    GetRoute('users/{id}', function ($id) {
        return [
            'version' => 'v1',
            'user_id' => $id,
            'method'  => 'header-based',
        ];
    })->where('id', '[0-9]+');
});

VersionedPrefix('v2', 'api', function () {
    GetRoute('test', function () {
        return ['version' => 'v2', 'method' => 'header-based'];
    });

    GetRoute('users/{id}', function ($id) {
        return [
            'version'  => 'v2',
            'user_id'  => $id,
            'method'   => 'header-based',
            'enhanced' => true,
        ];
    })->where('id', '[0-9]+');
});

// Version with middleware
VersionedGroup('v1', ['prefix' => 'api', 'middleware' => ['auth']], function () {
    GetRoute('profile', function () {
        return [
            'version' => 'v1',
            'message' => 'Protected profile endpoint',
        ];
    });
});

// Authentication routes (no versioning needed)
Prefix('auth', function () {
    PostRoute('login', function () {
        return ['message' => 'Login endpoint'];
    });

    PostRoute('register', function () {
        return ['message' => 'Register endpoint'];
    });
});

// Admin routes with combined prefix, middleware, and versioning
VersionedGroup('v1', ['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    GetRoute('dashboard', function () {
        return [
            'version' => 'v1',
            'message' => 'Admin dashboard',
        ];
    });

    RouteCollection('users', 'App\Controllers\AdminUsersController');
});
