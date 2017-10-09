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
        $requestIsStageFront = $request->is($stageFrontUrl);

        if ($disabled && $requestIsStageFront) {
            abort(404);
        }

        if ($unlocked || $disabled || $requestIsStageFront) {
            return $next($request);
        }

        return redirect($stageFrontUrl);
    }
}
