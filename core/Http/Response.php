<?php
namespace Rivulet\Http;

class Response
{
    private $content;
    private int $status;
    private array $headers;

    public function __construct($content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status  = $status;
        $this->headers = $headers;
    }

    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setStatusCode(int $code): self
    {
        $this->status = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function json($data, int $status = 200): self
    {
        $this->content                 = json_encode($data);
        $this->status                  = $status;
        $this->headers['Content-Type'] = 'application/json';

        return $this;
    }

    public function download(string $path, string $name = null, array $headers = []): self
    {
        if (! file_exists($path)) {
            abort(404, 'File not found');
        }

        $name = $name ?? basename($path);

        $this->headers = array_merge([
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $name . '"',
            'Content-Length'      => filesize($path),
        ], $headers);

        $this->content = file_get_contents($path);

        return $this;
    }

    public function file(string $path, array $headers = []): self
    {
        if (! file_exists($path)) {
            abort(404, 'File not found');
        }

        $mimeType = mime_content_type($path);

        $this->headers = array_merge([
            'Content-Type'   => $mimeType,
            'Content-Length' => filesize($path),
        ], $headers);

        $this->content = file_get_contents($path);

        return $this;
    }

    public function redirect(string $url, int $status = 302): self
    {
        $this->status              = $status;
        $this->headers['Location'] = $url;

        return $this;
    }

    public function withCookie(string $name, string $value, int $minutes = 0, string $path = '/', string $domain = null, bool $secure = false, bool $httpOnly = true): self
    {
        $expire = $minutes > 0 ? time() + ($minutes * 60) : 0;

        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);

        return $this;
    }

    public function send(): void
    {
        if (! headers_sent()) {
            http_response_code($this->status);

            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        echo $this->content;
    }

    public function __toString(): string
    {
        return (string) $this->content;
    }
}
