<?php return array (
  'app' => 
  array (
    'name' => 'Rivulet',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    'key' => '',
    'providers' => 
    array (
      0 => 'Rivulet\\Providers\\AppServiceProvider',
      1 => 'Rivulet\\Providers\\DatabaseServiceProvider',
      2 => 'Rivulet\\Providers\\RouteServiceProvider',
      3 => 'Rivulet\\Providers\\EventServiceProvider',
      4 => 'Rivulet\\Providers\\ViewsServiceProvider',
      5 => 'Rivulet\\Providers\\FilesystemServiceProvider',
      6 => 'Rivulet\\Providers\\MailServiceProvider',
      7 => 'Rivulet\\Providers\\QueueServiceProvider',
      8 => 'Rivulet\\Providers\\SessionServiceProvider',
      9 => 'Rivulet\\Providers\\CookiesServiceProvider',
      10 => 'Rivulet\\Providers\\HttpClientServiceProvider',
      11 => 'Rivulet\\Providers\\CacheServiceProvider',
      12 => 'Rivulet\\Providers\\NotificationServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Rivulet\\Rivulet',
      'Request' => 'Rivulet\\Http\\Request',
      'Response' => 'Rivulet\\Http\\Response',
      'Route' => 'Rivulet\\Routing\\Router',
      'DB' => 'Rivulet\\Database\\Connection',
      'Model' => 'Rivulet\\Model',
      'Controller' => 'Rivulet\\Controller',
      'View' => 'Rivulet\\Views\\View',
      'Cache' => 'Rivulet\\System\\Cache\\Cache',
      'Log' => 'Rivulet\\System\\Logging\\Logs',
      'Mail' => 'Rivulet\\Mail\\Mailer',
      'Queue' => 'Rivulet\\Queue\\Queue',
      'Session' => 'Rivulet\\Http\\Session\\Session',
      'Cookie' => 'Rivulet\\Http\\Cookies\\Cookies',
    ),
  ),
  'cookies' => 1,
  'cors' => 
  array (
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_methods' => 
    array (
      0 => 'GET',
      1 => 'POST',
      2 => 'PUT',
      3 => 'DELETE',
      4 => 'OPTIONS',
      5 => 'PATCH',
    ),
    'allowed_headers' => 
    array (
      0 => 'Content-Type',
      1 => 'Authorization',
      2 => 'X-Requested-With',
      3 => 'X-API-Token',
      4 => 'Accept',
      5 => 'Origin',
      6 => 'Cache-Control',
      7 => 'X-Requested-With',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 86400,
    'supports_credentials' => false,
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'rivulet',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
      ),
      'secondary' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'rivulet_secondary',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'rivulet',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
      ),
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => 'rivulet',
      ),
    ),
    'migrations' => 
    array (
      'table' => 'migrations',
      'path' => '/home/ammar/FrameWorks/rivulet/database/Migrations',
    ),
  ),
  'events' => 1,
  'filesystems' => 
  array (
    'default' => 'local',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/home/ammar/FrameWorks/rivulet/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/home/ammar/FrameWorks/rivulet/public/storage',
        'url' => 'http://localhost/storage',
        'visibility' => 'public',
      ),
      'uploads' => 
      array (
        'driver' => 'local',
        'root' => '/home/ammar/FrameWorks/rivulet/storage/uploads',
        'url' => 'http://localhost/storage/uploads',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'bucket' => '',
        'url' => NULL,
        'endpoint' => NULL,
        'use_path_style_endpoint' => false,
      ),
      'gcs' => 
      array (
        'driver' => 'gcs',
        'key_file' => NULL,
        'project_id' => NULL,
        'bucket' => NULL,
        'path_prefix' => '',
        'api_endpoint' => NULL,
      ),
    ),
    'links' => 
    array (
      '/home/ammar/FrameWorks/rivulet/public/storage' => '/home/ammar/FrameWorks/rivulet/storage/app/public',
      '/home/ammar/FrameWorks/rivulet/public/uploads' => '/home/ammar/FrameWorks/rivulet/storage/uploads',
    ),
  ),
  'logging' => 1,
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'smtp.mailtrap.io',
    'port' => '2525',
    'from' => 
    array (
      'address' => 'noreply@rivulet.com',
      'name' => 'Rivulet',
    ),
    'encryption' => NULL,
    'username' => NULL,
    'password' => NULL,
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/home/ammar/FrameWorks/rivulet/resources/views/vendor/mail',
      ),
    ),
    'log_channel' => NULL,
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
    ),
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'ses' => 
    array (
      'key' => '',
      'secret' => '',
      'region' => 'us-east-1',
    ),
    'sendgrid' => 
    array (
      'api_key' => NULL,
    ),
  ),
  'middleware' => 
  array (
    'global' => 
    array (
      0 => 'Rivulet\\Middleware\\CorsMiddleware',
    ),
    'route' => 
    array (
      'auth' => 'Rivulet\\Middleware\\AuthMiddleware',
      'rate.limit' => 'Rivulet\\Middleware\\RateLimitMiddleware',
    ),
    'groups' => 
    array (
      'web' => 
      array (
        0 => 'Rivulet\\Middleware\\CorsMiddleware',
      ),
      'api' => 
      array (
        0 => 'Rivulet\\Middleware\\CorsMiddleware',
        1 => 'Rivulet\\Middleware\\RateLimitMiddleware',
      ),
    ),
    'aliases' => 
    array (
      'auth' => 'Rivulet\\Middleware\\AuthMiddleware',
      'cors' => 'Rivulet\\Middleware\\CorsMiddleware',
      'throttle' => 'Rivulet\\Middleware\\RateLimitMiddleware',
    ),
  ),
  'queue' => 
  array (
    'default' => 'database',
    'connections' => 
    array (
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'connection' => 'mysql',
        'retry_after' => 90,
        'max_tries' => 3,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
        'max_tries' => 3,
      ),
      'rabbitmq' => 
      array (
        'driver' => 'rabbitmq',
        'host' => '127.0.0.1',
        'port' => 5672,
        'username' => 'guest',
        'password' => 'guest',
        'vhost' => '/',
        'queue' => 'default',
        'max_tries' => 3,
      ),
    ),
    'failed' => 
    array (
      'driver' => 'database',
      'table' => 'failed_jobs',
      'connection' => 'mysql',
    ),
    'supervisor' => 
    array (
      'processes' => 4,
      'sleep' => 3,
      'timeout' => 60,
      'tries' => 3,
    ),
  ),
  'routes' => 
  array (
    'api' => '/home/ammar/FrameWorks/rivulet/routes/api.php',
    'web' => '/home/ammar/FrameWorks/rivulet/routes/web.php',
  ),
  'schedule' => 1,
  'services' => 1,
  'session' => 1,
  'views' => 1,
  'websockets' => 
  array (
    'default' => 'pusher',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'app_id' => '',
        'app_key' => '',
        'app_secret' => '',
        'cluster' => 'mt1',
        'encrypted' => true,
      ),
      'ratchet' => 
      array (
        'driver' => 'ratchet',
        'host' => '0.0.0.0',
        'port' => 8080,
        'path' => '/ws',
        'middleware' => 
        array (
        ),
      ),
    ),
    'channels' => 
    array (
      'private' => 
      array (
        'prefix' => 'private-',
        'auth' => true,
      ),
      'presence' => 
      array (
        'prefix' => 'presence-',
        'auth' => true,
      ),
    ),
    'events' => 
    array (
      'connection' => true,
      'disconnection' => true,
      'message' => true,
      'broadcast' => true,
    ),
  ),
);