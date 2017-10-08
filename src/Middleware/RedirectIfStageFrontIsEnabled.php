<?php

namespace CodeZero\StageFront\Middleware;

use Closure;

class RedirectIfStageFrontIsEnabled
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
        $disabled = ! config('stagefront.enabled', false);
        $unlocked = session('stagefront.unlocked', false);
        $stageFrontUrl = trim(config('stagefront.url'), '/');

        if ($unlocked || $disabled || $request->is($stageFrontUrl)) {
            return $next($request);
        }

        return redirect($stageFrontUrl);
    }
}
