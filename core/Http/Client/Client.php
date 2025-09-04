<?php
namespace Rivulet\Http\Client;

class Client
{
    private array $defaultOptions = [
        'timeout'         => 30,
        'connect_timeout' => 10,
        'headers'         => [],
        'verify'          => true,
        'allow_redirects' => true,
        'max_redirects'   => 5,
        'decode_content'  => true,
    ];

    private array $options;
    private array $defaultHeaders = [];

    public function __construct(array $options = [])
    {
        $this->options        = array_merge($this->defaultOptions, $options);
        $this->defaultHeaders = [
            'User-Agent'      => 'Rivulet Framework HTTP Client/1.0',
            'Accept'          => 'application/json',
            'Accept-Encoding' => 'gzip, deflate',
        ];
    }

    public function get(string $url, array $options = []): Response
    {
        return $this->request('GET', $url, $options);
    }

    public function post(string $url, array $options = []): Response
    {
        return $this->request('POST', $url, $options);
    }

    public function put(string $url, array $options = []): Response
    {
        return $this->request('PUT', $url, $options);
    }

    public function patch(string $url, array $options = []): Response
    {
        return $this->request('PATCH', $url, $options);
    }

    public function delete(string $url, array $options = []): Response
    {
        return $this->request('DELETE', $url, $options);
    }

    public function head(string $url, array $options = []): Response
    {
        return $this->request('HEAD', $url, $options);
    }

    public function options(string $url, array $options = []): Response
    {
        return $this->request('OPTIONS', $url, $options);
    }

    public function request(string $method, string $url, array $options = []): Response
    {
        $options = array_merge($this->options, $options);
        $headers = array_merge($this->defaultHeaders, $options['headers'] ?? []);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_TIMEOUT        => $options['timeout'],
            CURLOPT_CONNECTTIMEOUT => $options['connect_timeout'],
            CURLOPT_SSL_VERIFYPEER => $options['verify'],
            CURLOPT_FOLLOWLOCATION => $options['allow_redirects'],
            CURLOPT_MAXREDIRS      => $options['max_redirects'],
            CURLOPT_ENCODING       => $options['decode_content'] ? '' : null,
        ]);

        if (! empty($headers)) {
            $headerArray = [];
            foreach ($headers as $key => $value) {
                $headerArray[] = "{$key}: {$value}";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        }

        if (isset($options['json'])) {
            $body = json_encode($options['json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            $headers['Content-Type'] = 'application/json';
        } elseif (isset($options['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
        } elseif (isset($options['form_params'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['form_params']));
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        } elseif (isset($options['multipart'])) {
            $postData = [];
            foreach ($options['multipart'] as $item) {
                if (isset($item['contents'])) {
                    if (isset($item['filename'])) {
                        $postData[$item['name']] = new \CURLFile($item['contents'], $item['type'] ?? null, $item['filename']);
                    } else {
                        $postData[$item['name']] = $item['contents'];
                    }
                }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $response   = curl_exec($ch);
        $error      = curl_error($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $totalTime  = curl_getinfo($ch, CURLINFO_TOTAL_TIME);

        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("HTTP request failed: {$error}");
        }

        $headers = substr($response, 0, $headerSize);
        $body    = substr($response, $headerSize);

        return new Response($httpCode, $this->parseHeaders($headers), $body, $totalTime);
    }

    private function parseHeaders(string $headerString): array
    {
        $headers = [];
        $lines   = explode("\r\n", trim($headerString));

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $value]       = explode(':', $line, 2);
                $headers[trim($key)] = trim($value);
            }
        }

        return $headers;
    }

    public function setDefaultOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function setDefaultHeaders(array $headers): self
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $headers);
        return $this;
    }

    public function withTimeout(int $seconds): self
    {
        $this->options['timeout'] = $seconds;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->options['headers'] = array_merge($this->options['headers'] ?? [], $headers);
        return $this;
    }

    public function withBasicAuth(string $username, string $password): self
    {
        $this->options['auth'] = [$username, $password];
        return $this;
    }

    public function withBearerToken(string $token): self
    {
        return $this->withHeaders(['Authorization' => 'Bearer ' . $token]);
    }

    public function withoutRedirect(): self
    {
        $this->options['allow_redirects'] = false;
        return $this;
    }

    public function withoutVerify(): self
    {
        $this->options['verify'] = false;
        return $this;
    }
}
