<?php

namespace CodeZero\StageFront\Controllers;

use CodeZero\StageFront\Rules\LoginAndPasswordMatch;
use Illuminate\Routing\Controller;

class StageFrontController extends Controller
{
    /**
     * Show the StageFront login screen.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $liveSite = config('stagefront.live_site');

        if ($liveSite) {
            $liveSite = [
                'url' => $liveSite,
                'host' => parse_url($liveSite, PHP_URL_HOST),
            ];
        }

        $this->disableLaravelDebugbar();

        return view('stagefront::login', compact('liveSite'));
    }

    /**
     * Attempt to log a user in to StageFront.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        request()->validate([
            'login' => ['required'],
            'password' => ['required', new LoginAndPasswordMatch()],
        ], [
            'login.required' => trans('stagefront::errors.login.required'),
            'password.required' => trans('stagefront::errors.password.required'),
        ]);

        session()->put('stagefront.unlocked', true);

        return redirect()->intended('/');
    }

    /**
     * Disable Laravel Debugbar if it is loaded.
     *
     * @return void
     */
    protected function disableLaravelDebugbar()
    {
        if (class_exists('\Debugbar')) {
            \Debugbar::disable();
        }
    }
}
