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
        $stageFrontUrl = config('stagefront.url');
        $ignoredUrls = config('stagefront.ignore_urls', []);
        array_push($ignoredUrls, $stageFrontUrl);

        if ($unlocked || $disabled || $this->urlIsIgnored($request, $ignoredUrls)) {
            return $next($request);
        }

        return redirect($stageFrontUrl);
    }

    /**
     * Check if a URL should be ignored.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $ignoredUrls
     *
     * @return bool
     */
    protected function urlIsIgnored($request, $ignoredUrls)
    {
        foreach ($ignoredUrls as $url) {
            $url = trim($url, '/');

            if ($request->is($url)) {
                return true;
            }
        }

        return false;
    }
}
