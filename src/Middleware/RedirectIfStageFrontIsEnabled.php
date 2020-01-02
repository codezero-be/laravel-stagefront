<?php

namespace CodeZero\StageFront\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

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
        $disabled = ! Config::get('stagefront.enabled', false);
        $unlocked = Session::get('stagefront.unlocked', false);
        $stageFrontUrl = Config::get('stagefront.url');
        $ignoredUrls = Config::get('stagefront.ignore_urls', []);
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
