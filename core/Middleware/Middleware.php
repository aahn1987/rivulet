<?php
namespace Rivulet\Middleware;

use Rivulet\Http\Request;

abstract class Middleware
{
    abstract public function handle(Request $request, \Closure $next);
}
