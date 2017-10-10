<?php

namespace CodeZero\StageFront\Middleware;

use Closure;

class DisableLaravelDebugbar
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (class_exists('\Debugbar')) {
            \Debugbar::disable();
        }

        return $next($request);
    }
}
