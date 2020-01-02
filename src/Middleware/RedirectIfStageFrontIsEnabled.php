<?php

namespace CodeZero\StageFront\Middleware;

use Closure;
use CodeZero\StageFront\Shield;
use Illuminate\Support\Facades\Config;

class RedirectIfStageFrontIsEnabled
{
    /**
     * Shield instance.
     *
     * @var \CodeZero\StageFront\Shield
     */
    protected $shield;

    /**
     * Create a new RedirectIfStageFrontIsEnabled instance.
     *
     * @param \CodeZero\StageFront\Shield $shield
     */
    public function __construct(Shield $shield)
    {
        $this->shield = $shield;
    }

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
        $stageFrontUrl = Config::get('stagefront.url');

        if ($this->shield->shouldDenyAccess()) {
            return abort(403);
        }

        if ($this->shield->requiresLogin()) {
            return redirect($stageFrontUrl);
        }

        return $next($request);
    }
}
