<?php
namespace Rivulet\Http;

use Rivulet\Validation\Validator;

class Request
{
    private array $query;
    private array $request;
    private array $attributes;
    private array $cookies;
    private array $files;
    private array $server;
    private $content;

    public function __construct()
    {
        $this->query      = $_GET;
        $this->request    = $_POST;
        $this->attributes = [];
        $this->cookies    = $_COOKIE;
        $this->files      = $_FILES;
        $this->server     = $_SERVER;
        $this->content    = null;
    }

    public static function capture(): self
    {
        return new static();
    }

    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function url(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    public function fullUrl(): string
    {
        return $this->scheme() . '://' . $this->host() . $this->url();
    }

    public function scheme(): string
    {
        return (! empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') ? 'https' : 'http';
    }

    public function host(): string
    {
        return $this->server['HTTP_HOST'] ?? 'localhost';
    }

    public function path(): string
    {
        return parse_url($this->url(), PHP_URL_PATH);
    }

    public function is(string $pattern): bool
    {
        return fnmatch($pattern, $this->path());
    }

    public function isMethod(string $method): bool
    {
        return strcasecmp($this->method(), $method) === 0;
    }

    public function header(string $key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$key] ?? $default;
    }

    public function headers(): array
    {
        $headers = [];

        foreach ($this->server as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header           = str_replace('_', '-', substr($key, 5));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization');

        if ($header && preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function input(string $key, $default = null)
    {
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    public function query(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }

    public function only(array $keys): array
    {
        $data = [];

        foreach ($keys as $key) {
            if ($this->has($key)) {
                $data[$key] = $this->input($key);
            }
        }

        return $data;
    }

    public function except(array $keys): array
    {
        $data = $this->all();

        foreach ($keys as $key) {
            unset($data[$key]);
        }

        return $data;
    }

    public function has(string $key): bool
    {
        return isset($this->request[$key]) || isset($this->query[$key]);
    }

    public function filled(string $key): bool
    {
        $value = $this->input($key);
        return ! is_null($value) && $value !== '';
    }

    public function missing(string $key): bool
    {
        return ! $this->has($key);
    }

    public function json(string $key = null, $default = null)
    {
        $content = $this->getContent();

        if (! $content) {
            return $default;
        }

        $data = json_decode($content, true);

        if (is_null($key)) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    public function getContent(): string
    {
        if ($this->content === null) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    public function isJson(): bool
    {
        return strpos($this->header('Content-Type'), 'application/json') !== false;
    }

    public function ip(): string
    {
        $keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        foreach ($keys as $key) {
            if (! empty($this->server[$key])) {
                return $this->server[$key];
            }
        }

        return '0.0.0.0';
    }

    public function userAgent(): ?string
    {
        return $this->header('User-Agent');
    }

    public function cookie(string $key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function validate(array $rules): array
    {
        $validator = new Validator($this->all(), $rules);

        if ($validator->fails()) {
            abort(422, json_encode(['errors' => $validator->errors()]));
        }

        return $validator->validated();
    }

    public function __get(string $key)
    {
        return $this->input($key);
    }

    public function __isset(string $key)
    {
        return $this->has($key);
    }
    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }
}
