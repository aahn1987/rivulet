<?php
namespace Rivulet\Middleware;

use Rivulet\Auth\TokenGuard;
use Rivulet\Http\Request;

class AuthMiddleware extends Middleware
{
    protected string $guard = 'api';

    public function handle(Request $request, \Closure $next)
    {
        $token = $request->bearerToken() ?? $request->header('X-API-Token');

        if (! $token) {
            abort(401, 'Authentication token required');
        }

        $guard = new TokenGuard();
        $user  = $guard->authenticate($token);

        if (! $user) {
            abort(401, 'Invalid authentication token');
        }

        $request->setAttribute('user', $user);

        return $next($request);
    }
}
