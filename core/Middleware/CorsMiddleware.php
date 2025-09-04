<?php
namespace Rivulet\Middleware;

use Rivulet\Http\Request;
use Rivulet\Http\Response;

class CorsMiddleware extends Middleware
{
    private array $allowedOrigins = [];
    private array $allowedMethods = [];
    private array $allowedHeaders = [];

    public function __construct()
    {
        $this->allowedOrigins = config('cors.allowed_origins', ['*']);
        $this->allowedMethods = config('cors.allowed_methods', ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']);
        $this->allowedHeaders = config('cors.allowed_headers', ['Content-Type', 'Authorization', 'X-Requested-With']);
    }

    public function handle(Request $request, \Closure $next)
    {
        $origin = $request->header('Origin');

        if ($origin && in_array($origin, $this->allowedOrigins) || in_array('*', $this->allowedOrigins)) {
            $response = $next($request);

            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods));
            $response->header('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders));

            return $response;
        }

        if ($request->method() === 'OPTIONS') {
            $response = new Response('', 200);
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods));
            $response->header('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders));
            $response->header('Access-Control-Max-Age', '86400');

            return $response;
        }

        return $next($request);
    }
}
