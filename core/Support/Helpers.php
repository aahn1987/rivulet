<?php

// ============================================================================
// CORE FRAMEWORK FUNCTIONS (Highest Priority)
// ============================================================================

if (! function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (strlen($value) > 1 && str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (! function_exists('config')) {
    function config(string $key, $default = null)
    {
        static $config = [];

        if (empty($config)) {
            $config = require configPath('app.php');
        }

        $keys  = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (! isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (! function_exists('app')) {
    function app(string $abstract = null)
    {
        $container = \Rivulet\Rivulet::getInstance();

        if ($abstract === null) {
            return $container;
        }

        return $container->make($abstract);
    }
}

// ============================================================================
// PATH HELPERS (High Priority)
// ============================================================================

if (! function_exists('basePath')) {
    function basePath(string $path = ''): string
    {
        return rtrim(dirname(__DIR__, 2), DIRECTORY_SEPARATOR) . ($path  ?DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
    }
}

if (! function_exists('appPath')) {
    function appPath(string $path = ''): string
    {
        return basePath('app' . ($path  ?DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('configPath')) {
    function configPath(string $path = ''): string
    {
        return basePath('config' . ($path  ?DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('storagePath')) {
    function storagePath(string $path = ''): string
    {
        return basePath('storage' . ($path  ?DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('publicPath')) {
    function publicPath(string $path = ''): string
    {
        return basePath('public' . ($path  ?DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('resourcePath')) {
    function resourcePath(string $path = ''): string
    {
        return basePath('resources' . ($path  ?DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('storageLocation')) {
    function storageLocation(string $path = ''): string
    {
        return storagePath($path);
    }
}

if (! function_exists('publicLocation')) {
    function publicLocation(string $path = ''): string
    {
        return publicPath($path);
    }
}

if (! function_exists('resourceLocation')) {
    function resourceLocation(string $path = ''): string
    {
        return resourcePath($path);
    }
}

if (! function_exists('databaseLocation')) {
    function databaseLocation(string $path = ''): string
    {
        return basePath('database' . ($path  ?DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

// ============================================================================
// CONTAINER & SERVICE RESOLUTION (High Priority)
// ============================================================================

if (! function_exists('resolve')) {
    function resolve(string $abstract)
    {
        return app()->make($abstract);
    }
}

if (! function_exists('make')) {
    function make(string $abstract, array $parameters = [])
    {
        return app()->make($abstract, $parameters);
    }
}

if (! function_exists('bind')) {
    function bind(string $abstract, $concrete = null, bool $shared = false): void
    {
        app()->bind($abstract, $concrete, $shared);
    }
}

if (! function_exists('singleton')) {
    function singleton(string $abstract, $concrete = null): void
    {
        app()->singleton($abstract, $concrete);
    }
}

if (! function_exists('bound')) {
    function bound(string $abstract): bool
    {
        return app()->bound($abstract);
    }
}

if (! function_exists('resolved')) {
    function resolved(string $abstract): bool
    {
        return app()->resolved($abstract);
    }
}

if (! function_exists('factory')) {
    function factory(string $abstract): \Closure
    {
        return app()->factory($abstract);
    }
}

if (! function_exists('tagged')) {
    function tagged(string $tag): array
    {
        return app()->tagged($tag);
    }
}

if (! function_exists('extend')) {
    function extend(string $abstract, \Closure $closure): void
    {
        app()->extend($abstract, $closure);
    }
}

if (! function_exists('when')) {
    function when(string $concrete)
    {
        return app()->when($concrete);
    }
}

if (! function_exists('alias')) {
    function alias(string $abstract, string $alias): void
    {
        app()->alias($abstract, $alias);
    }
}

if (! function_exists('flush')) {
    function flush(): void
    {
        app()->flush();
    }
}

// ============================================================================
// HTTP & RESPONSE HELPERS (High Priority)
// ============================================================================

if (! function_exists('Request')) {
    function Request()
    {
        return new Rivulet\Http\Request();
    }
}

if (! function_exists('response')) {
    function response($data = [], int $status = 200, array $headers = [])
    {
        return new Rivulet\Http\Response($data, $status, $headers);
    }
}

if (! function_exists('jsonResponse')) {
    function jsonResponse($data = [], int $status = 200, string $message = null)
    {
        $response = [
            'data'    => $data,
            'status'  => $status,
            'message' => $message,
        ];

        if ($message === null) {
            unset($response['message']);
        }

        return response()->json($response, $status);
    }
}

if (! function_exists('abort')) {
    function abort(int $code = 404, string $message = '', array $headers = [])
    {
        http_response_code($code);

        foreach ($headers as $key => $value) {
            header("$key: $value");
        }

        if (! empty($message)) {
            echo $message;
        }

        exit;
    }
}

if (! function_exists('asset')) {
    function asset(string $path): string
    {
        return rtrim(env('APP_URL'), '/') . '/' . ltrim($path, '/');
    }
}

// ============================================================================
// ROUTING HELPERS (High Priority)
// ============================================================================

if (! function_exists('Route')) {
    function Route(string $method, string $uri, $action): \Rivulet\Routing\Route
    {
        $router = new \Rivulet\Routing\Router();
        return $router->match([$method], $uri, $action);
    }
}

if (! function_exists('GetRoute')) {
    function GetRoute(string $uri, $action): \Rivulet\Routing\Route
    {
        $router = new \Rivulet\Routing\Router();
        return $router->get($uri, $action);
    }
}

if (! function_exists('PostRoute')) {
    function PostRoute(string $uri, $action): \Rivulet\Routing\Route
    {
        $router = new \Rivulet\Routing\Router();
        return $router->post($uri, $action);
    }
}

if (! function_exists('PutRoute')) {
    function PutRoute(string $uri, $action): \Rivulet\Routing\Route
    {
        $router = new \Rivulet\Routing\Router();
        return $router->put($uri, $action);
    }
}

if (! function_exists('DeleteRoute')) {
    function DeleteRoute(string $uri, $action): \Rivulet\Routing\Route
    {
        $router = new \Rivulet\Routing\Router();
        return $router->delete($uri, $action);
    }
}

if (! function_exists('PatchRoute')) {
    function PatchRoute(string $uri, $action): \Rivulet\Routing\Route
    {
        $router = new \Rivulet\Routing\Router();
        return $router->patch($uri, $action);
    }
}

if (! function_exists('Prefix')) {
    function Prefix(string $prefix, \Closure $callback): void
    {
        $router = new \Rivulet\Routing\Router();
        $router->group(['prefix' => $prefix], $callback);
    }
}

if (! function_exists('Middleware')) {
    function Middleware(array $middleware, \Closure $callback): void
    {
        $router = new \Rivulet\Routing\Router();
        $router->group(['middleware' => $middleware], $callback);
    }
}

if (! function_exists('RouteGroup')) {
    function RouteGroup(string $prefix, array $middleware, \Closure $callback): void
    {
        $router = new \Rivulet\Routing\Router();
        $router->group(['prefix' => $prefix, 'middleware' => $middleware], $callback);
    }
}

if (! function_exists('RouteCollection')) {
    function RouteCollection(string $uri, string $controller): void
    {
        $router = new \Rivulet\Routing\Router();
        $router->resource($uri, $controller);
    }
}

if (! function_exists('FileRoute')) {
    function FileRoute(string $uri, string $path): void
    {
        $router = new \Rivulet\Routing\Router();
        $router->file($uri, $path);
    }
}

if (! function_exists('version')) {
    function version(string $version): \Rivulet\Routing\Router
    {
        $router = new \Rivulet\Routing\Router();
        return $router->version($version);
    }
}

if (! function_exists('VersionedPrefix')) {
    function VersionedPrefix(string $version, string $prefix, \Closure $callback): void
    {
        $router = new \Rivulet\Routing\Router();
        $router->version($version)->group(['prefix' => $prefix], $callback);
    }
}

if (! function_exists('VersionedGroup')) {
    function VersionedGroup(string $version, array $attributes, \Closure $callback): void
    {
        $router = new \Rivulet\Routing\Router();
        $router->version($version)->group($attributes, $callback);
    }
}

// ============================================================================
// VIEW & TEMPLATE HELPERS (Medium-High Priority)
// ============================================================================

if (! function_exists('view')) {
    function view(string $template, array $data = [])
    {
        return app('view')->render($template, $data);
    }
}

if (! function_exists('render')) {
    function render(string $template, array $data = []): string
    {
        return view($template, $data);
    }
}

if (! function_exists('escape')) {
    function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (! function_exists('e')) {
    function e(string $value): string
    {
        return escape($value);
    }
}

// ============================================================================
// SESSION & COOKIE HELPERS (Medium-High Priority)
// ============================================================================

if (! function_exists('session')) {
    function session()
    {
        return new \Rivulet\Http\Session\Session();
    }
}

if (! function_exists('session_get')) {
    function session_get(string $key, $default = null)
    {
        return session()->get($key, $default);
    }
}

if (! function_exists('session_put')) {
    function session_put(string $key, $value): void
    {
        session()->put($key, $value);
    }
}

if (! function_exists('session_forget')) {
    function session_forget(string $key): void
    {
        session()->forget($key);
    }
}

if (! function_exists('cookie')) {
    function cookie()
    {
        return new \Rivulet\Http\Cookies\Cookies();
    }
}

if (! function_exists('cookie_set')) {
    function cookie_set(string $name, $value, array $options = []): void
    {
        cookie()->set($name, $value, $options);
    }
}

if (! function_exists('cookie_get')) {
    function cookie_get(string $name, $default = null)
    {
        return cookie()->get($name, $default);
    }
}

if (! function_exists('cookie_forget')) {
    function cookie_forget(string $name): void
    {
        cookie()->forget($name);
    }
}

// ============================================================================
// AUTHENTICATION HELPERS (Medium-High Priority)
// ============================================================================

if (! function_exists('auth')) {
    function auth()
    {
        return new \Rivulet\Auth\TokenGuard();
    }
}

if (! function_exists('user')) {
    function user()
    {
        return request()->getAttribute('user');
    }
}

if (! function_exists('check')) {
    function check(): bool
    {
        return user() !== null;
    }
}

if (! function_exists('guest')) {
    function guest(): bool
    {
        return ! check();
    }
}

if (! function_exists('hash')) {
    function hash(string $password): string
    {
        return \Rivulet\Auth\Password::hash($password);
    }
}

if (! function_exists('hashCheck')) {
    function hashCheck(string $password, string $hash): bool
    {
        return \Rivulet\Auth\Password::verify($password, $hash);
    }
}

if (! function_exists('password')) {
    function password(int $length = 12): string
    {
        return \Rivulet\Auth\Password::generate($length);
    }
}

// ============================================================================
// VALIDATION HELPERS (Medium Priority)
// ============================================================================

if (! function_exists('validator')) {
    function validator(array $data, array $rules, array $messages = [])
    {
        return new \Rivulet\Validation\Validator($data, $rules, $messages);
    }
}

if (! function_exists('validate')) {
    function validate(array $data, array $rules, array $messages = []): array
    {
        $validator = new \Rivulet\Validation\Validator($data, $rules, $messages);

        if ($validator->fails()) {
            abort(422, json_encode(['errors' => $validator->errors()]));
        }

        return $validator->validated();
    }
}

// ============================================================================
// DATABASE HELPERS (Medium Priority)
// ============================================================================

if (! function_exists('db')) {
    function db()
    {
        return app('db');
    }
}

// ============================================================================
// CACHING HELPERS (Medium Priority)
// ============================================================================

if (! function_exists('cache')) {
    function cache()
    {
        return new \Rivulet\System\Cache\Cache();
    }
}

if (! function_exists('cache_get')) {
    function cache_get(string $key, $default = null)
    {
        return cache()->get($key, $default);
    }
}

if (! function_exists('cache_put')) {
    function cache_put(string $key, $value, int $seconds = 3600): bool
    {
        return cache()->put($key, $value, $seconds);
    }
}

if (! function_exists('cache_forget')) {
    function cache_forget(string $key): bool
    {
        return cache()->forget($key);
    }
}

if (! function_exists('cache_has')) {
    function cache_has(string $key): bool
    {
        return cache()->has($key);
    }
}

// ============================================================================
// LOGGING HELPERS (Medium Priority)
// ============================================================================

if (! function_exists('logs')) {
    function logs()
    {
        return new \Rivulet\System\Logging\Logs();
    }
}

if (! function_exists('log')) {
    function log()
    {
        return app('log');
    }
}

if (! function_exists('log_debug')) {
    function log_debug(string $message, array $context = []): void
    {
        logs()->debug($message, $context);
    }
}

if (! function_exists('log_info')) {
    function log_info(string $message, array $context = []): void
    {
        logs()->info($message, $context);
    }
}

if (! function_exists('log_error')) {
    function log_error(string $message, array $context = []): void
    {
        logs()->error($message, $context);
    }
}

// ============================================================================
// QUEUE & JOB HELPERS (Medium Priority)
// ============================================================================

if (! function_exists('queue')) {
    function queue()
    {
        return new \Rivulet\Queue\Queue();
    }
}

if (! function_exists('dispatch')) {
    function dispatch($job, string $queue = 'default'): string
    {
        $queueInstance = new \Rivulet\Queue\Queue();
        return $queueInstance->push($job, $queue);
    }
}

if (! function_exists('dispatchNow')) {
    function dispatchNow($job): void
    {
        $job->handle();
    }
}

if (! function_exists('dispatchLater')) {
    function dispatchLater(int $delay, $job, string $queue = 'default'): string
    {
        $queueInstance = new \Rivulet\Queue\Queue();
        return $queueInstance->later($delay, $job, $queue);
    }
}

// ============================================================================
// SCHEDULING HELPERS (Medium Priority)
// ============================================================================

if (! function_exists('schedule')) {
    function schedule()
    {
        return new \Rivulet\Queue\Scheduler();
    }
}

if (! function_exists('everyMinute')) {
    function everyMinute(callable $callback): void
    {
        schedule()->everyMinute($callback);
    }
}

if (! function_exists('everyFiveMinutes')) {
    function everyFiveMinutes(callable $callback): void
    {
        schedule()->everyFiveMinutes($callback);
    }
}

if (! function_exists('hourly')) {
    function hourly(callable $callback): void
    {
        schedule()->hourly($callback);
    }
}

if (! function_exists('daily')) {
    function daily(callable $callback): void
    {
        schedule()->daily($callback);
    }
}

// ============================================================================
// EVENT SYSTEM HELPERS (Medium Priority)
// ============================================================================

if (! function_exists('event')) {
    function event(string $event, array $data = []): \Rivulet\Events\Event
    {
        $dispatcher = new \Rivulet\Events\Dispatcher();
        return $dispatcher->dispatch($event, $data);
    }
}

if (! function_exists('triggerEvent')) {
    function triggerEvent(string $event, array $data = []): \Rivulet\Events\Event
    {
        return event($event, $data);
    }
}

if (! function_exists('listen')) {
    function listen(string $event, $listener, int $priority = 0): void
    {
        $dispatcher = new \Rivulet\Events\Dispatcher();
        $dispatcher->listen($event, $listener, $priority);
    }
}

if (! function_exists('subscribe')) {
    function subscribe($subscriber): void
    {
        $dispatcher = new \Rivulet\Events\Dispatcher();
        $dispatcher->subscribe($subscriber);
    }
}

// ============================================================================
// HTTP CLIENT HELPERS (Medium Priority)
// ============================================================================

if (! function_exists('http')) {
    function http(array $options = [])
    {
        return new \Rivulet\Http\Client\Client($options);
    }
}

if (! function_exists('httpClient')) {
    function httpClient(array $options = [])
    {
        return new \Rivulet\Http\Client\Client($options);
    }
}

if (! function_exists('get')) {
    function get(string $url, array $options = [])
    {
        return (new \Rivulet\Http\Client\Client())->get($url, $options);
    }
}

if (! function_exists('post')) {
    function post(string $url, array $options = [])
    {
        return (new \Rivulet\Http\Client\Client())->post($url, $options);
    }
}

if (! function_exists('put')) {
    function put(string $url, array $options = [])
    {
        return (new \Rivulet\Http\Client\Client())->put($url, $options);
    }
}

if (! function_exists('patch')) {
    function patch(string $url, array $options = [])
    {
        return (new \Rivulet\Http\Client\Client())->patch($url, $options);
    }
}

if (! function_exists('delete')) {
    function delete(string $url, array $options = [])
    {
        return (new \Rivulet\Http\Client\Client())->delete($url, $options);
    }
}

// ============================================================================
// FILE SYSTEM HELPERS (Medium-Low Priority)
// ============================================================================

if (! function_exists('storage')) {
    function storage()
    {
        return app('filesystem');
    }
}

if (! function_exists('upload')) {
    function upload(array $file, string $destination, string $filename = null): bool
    {
        return storage()->upload($file, $destination, $filename);
    }
}

if (! function_exists('download')) {
    function download(string $url, string $destination): bool
    {
        return storage()->download($url, $destination);
    }
}

if (! function_exists('makeDirectory')) {
    function makeDirectory(string $path, int $mode = 0755, bool $recursive = false, bool $force = false): bool
    {
        return storage()->makeDirectory($path, $mode, $recursive, $force);
    }
}

if (! function_exists('deleteDirectory')) {
    function deleteDirectory(string $directory, bool $preserve = false): bool
    {
        return storage()->deleteDirectory($directory, $preserve);
    }
}

if (! function_exists('zip')) {
    function zip(string $source, string $destination): bool
    {
        return storage()->zip($source, $destination);
    }
}

if (! function_exists('extract')) {
    function extract(string $zipFile, string $destination): bool
    {
        return storage()->extract($zipFile, $destination);
    }
}

// ============================================================================
// MAIL HELPERS (Medium-Low Priority)
// ============================================================================

if (! function_exists('mail')) {
    function mail()
    {
        return app('mail');
    }
}

if (! function_exists('sendMail')) {
    function sendMail(string $to, string $subject, string $body, array $data = []): bool
    {
        $mailer = new \Rivulet\Mail\Mailer();
        return $mailer->to($to)->subject($subject)->body($body)->send();
    }
}

if (! function_exists('mailTo')) {
    function mailTo(string $to, \Closure $callback): bool
    {
        $mailer  = new \Rivulet\Mail\Mailer();
        $message = $mailer->to($to);
        $callback($message);
        return $message->send();
    }
}

if (! function_exists('queueMail')) {
    function queueMail(string $template, array $data = [], \Closure $callback = null): void
    {
        $mailer = new \Rivulet\Mail\Mailer();
        $mailer->queue($template, $data, $callback);
    }
}

// ============================================================================
// NOTIFICATION HELPERS (Medium-Low Priority)
// ============================================================================

if (! function_exists('notify')) {
    function notify($notification, $notifiables = null): bool
    {
        $manager = new \Rivulet\Notifications\NotificationManager();
        return $manager->send($notification, $notifiables);
    }
}

if (! function_exists('notifyFirebase')) {
    function notifyFirebase(array $data): bool
    {
        $driver = new \Rivulet\Notifications\Drivers\FirebaseDriver(
            env('FIREBASE_SERVER_KEY'),
            env('FIREBASE_SENDER_ID')
        );

        return $driver->send($data);
    }
}

if (! function_exists('notifySlack')) {
    function notifySlack(array $data): bool
    {
        $driver = new \Rivulet\Notifications\Drivers\SlackDriver(env('SLACK_WEBHOOK_URL'));
        return $driver->send($data);
    }
}

if (! function_exists('notifySms')) {
    function notifySms(string $to, string $message): bool
    {
        $driver = new \Rivulet\Notifications\Drivers\SmsDriver(
            env('TWILIO_SID'),
            env('TWILIO_AUTH_TOKEN'),
            env('TWILIO_PHONE_NUMBER')
        );

        return $driver->send([
            'to'   => $to,
            'body' => $message,
        ]);
    }
}

if (! function_exists('notifyWhatsapp')) {
    function notifyWhatsapp(string $to, string $message): bool
    {
        $driver = new \Rivulet\Notifications\Drivers\WhatsappDriver(
            env('WHATSAPP_TOKEN'),
            env('WHATSAPP_PHONE_NUMBER_ID')
        );

        return $driver->send([
            'to'   => $to,
            'body' => $message,
        ]);
    }
}

// ============================================================================
// WEBSOCKET HELPERS (Low Priority)
// ============================================================================

if (! function_exists('websocket')) {
    function websocket()
    {
        return new \Rivulet\Websockets\WebSocketManager();
    }
}

if (! function_exists('broadcast')) {
    function broadcast(string $channel, string $event, $data): bool
    {
        return websocket()->broadcast($channel, $event, $data);
    }
}

if (! function_exists('wsAuthenticate')) {
    function wsAuthenticate(string $channel, string $socketId, array $data = []): array
    {
        return websocket()->authenticate($channel, $socketId, $data);
    }
}

if (! function_exists('wsStart')) {
    function wsStart(): void
    {
        websocket()->startServer();
    }
}

// ============================================================================
// UTILITY & HELPER FUNCTIONS (Low Priority)
// ============================================================================

if (! function_exists('strRandom')) {
    function strRandom(int $length = 16): string
    {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = random_int(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
    }
}

if (! function_exists('now')) {
    function now(DateTimeZone | string $timezone = null): DateTime
    {
        return new DateTime('now', $timezone ? ($timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone)) : null);
    }
}

if (! function_exists('class_basename')) {
    function class_basename($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}

// ============================================================================
// DEBUG & DEVELOPMENT HELPERS (Low Priority)
// ============================================================================

if (! function_exists('dd')) {
    function dd(...$vars): void
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die(1);
    }
}

if (! function_exists('dump')) {
    function dump(...$vars): void
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
    }
}

if (! function_exists('debug')) {
    function debug($var)
    {
        \Rivulet\System\Debug\Debug::dump($var);
    }
}

if (! function_exists('debug_timer')) {
    function debug_timer(string $name, callable $callback)
    {
        return \Rivulet\System\Debug\Debug::measure($name, $callback);
    }
}
