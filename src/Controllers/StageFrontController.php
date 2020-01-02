<?php

namespace CodeZero\StageFront\Controllers;

use CodeZero\StageFront\Rules\LoginAndPasswordMatch;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class StageFrontController extends Controller
{
    /**
     * Show the StageFront login screen.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        if (Session::get('stagefront.unlocked') === true) {
            return redirect('/');
        }

        $liveSite = Config::get('stagefront.live_site');

        if ($liveSite) {
            $liveSite = [
                'url' => $liveSite,
                'host' => parse_url($liveSite, PHP_URL_HOST),
            ];
        }

        Session::flash('url.intended', Session::previousUrl());

        return view('stagefront::login', compact('liveSite'));
    }

    /**
     * Attempt to log a user in to StageFront.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        Request::validate([
            'login' => ['required'],
            'password' => ['required', new LoginAndPasswordMatch()],
        ], [
            'login.required' => Lang::get('stagefront::errors.login.required'),
            'password.required' => Lang::get('stagefront::errors.password.required'),
        ]);

        Session::put('stagefront.unlocked', true);

        return redirect()->intended('/');
    }
}
