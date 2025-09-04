<?php
namespace Rivulet\Http\Client;

class Response
{
    private int $statusCode;
    private array $headers;
    private string $body;
    private float $duration;

    public function __construct(int $statusCode, array $headers, string $body, float $duration)
    {
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
        $this->body       = $body;
        $this->duration   = $duration;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function json($assoc = true)
    {
        return json_decode($this->body, $assoc);
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }

    public function isError(): bool
    {
        return $this->isClientError() || $this->isServerError();
    }

    public function throw(): self
    {
        if ($this->isError()) {
            throw new \RuntimeException("HTTP request failed with status {$this->statusCode}: {$this->body}");
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->body;
    }
}
