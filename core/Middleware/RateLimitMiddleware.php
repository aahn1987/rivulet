<?php
namespace Rivulet\Middleware;

use Rivulet\Http\Request;
use Rivulet\System\Cache\Cache;

class RateLimitMiddleware extends Middleware
{
    private int $maxAttempts;
    private int $decayMinutes;
    private string $prefix = 'rate_limit:';

    public function __construct(int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $this->maxAttempts  = $maxAttempts;
        $this->decayMinutes = $decayMinutes;
    }

    public function handle(Request $request, \Closure $next)
    {
        $key   = $this->resolveRequestSignature($request);
        $cache = new Cache();

        $attempts = $cache->get($key, 0);

        if ($attempts >= $this->maxAttempts) {
            abort(429, 'Too many requests', [
                'Retry-After'           => $this->decayMinutes * 60,
                'X-RateLimit-Limit'     => $this->maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        $cache->put($key, $attempts + 1, $this->decayMinutes * 60);

        $response = $next($request);

        $response->header('X-RateLimit-Limit', $this->maxAttempts);
        $response->header('X-RateLimit-Remaining', $this->maxAttempts - $attempts - 1);

        return $response;
    }

    private function resolveRequestSignature(Request $request): string
    {
        return $this->prefix . sha1(
            $request->ip() . '|' . $request->path()
        );
    }
}
