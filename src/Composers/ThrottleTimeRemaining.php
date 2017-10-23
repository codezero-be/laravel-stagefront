<?php

namespace CodeZero\StageFront\Composers;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\View\View;

class ThrottleTimeRemaining
{
    /**
     * Provide the view with the time remaining on the throttle.
     *
     * @param \Illuminate\Contracts\View\View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('timeRemaining', $this->getTimeRemaining());
    }

    /**
     * Get the time remaining on the throttle in a human readable format.
     *
     * @return string
     */
    protected function getTimeRemaining()
    {
        if ( ! $key = $this->getCacheKey()) {
            return trans('stagefront::errors.throttled.moment');
        }

        $secondsRemaining = $this->getSecondsRemaining($key);

        Carbon::setLocale(app()->getLocale());

        return Carbon::now()
            ->addSeconds($secondsRemaining)
            ->diffForHumans(null, true);
    }

    /**
     * Resolve the cache key for the throttle info.
     * See `resolveRequestSignature` method:
     * https://github.com/illuminate/routing/blob/master/Middleware/ThrottleRequests.php#L88
     *
     * @return string|null
     */
    protected function getCacheKey()
    {
        $request = request();

        if ($user = $request->user()) {
            return sha1($user->getAuthIdentifier());
        }

        if ($route = $request->route()) {
            return sha1($route->getDomain().'|'.$request->ip());
        }

        return null;
    }

    /**
     * Get the remaining seconds on the throttle.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getSecondsRemaining($key)
    {
        return app(RateLimiter::class)->availableIn($key);
    }
}
