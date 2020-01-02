<?php

use CodeZero\StageFront\Controllers\StageFrontController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

if (Config::get('stagefront.enabled') === true) {

    Route::group(['middleware' => Config::get('stagefront.middleware')], function () {

        $url = Config::get('stagefront.url');
        $throttle = Config::get('stagefront.throttle');
        $tries = Config::get('stagefront.throttle_tries');
        $delay = Config::get('stagefront.throttle_delay');
        $middleware = $throttle ? "throttle:{$tries},{$delay}" : [];

        Route::get($url, StageFrontController::class.'@create');
        Route::post($url, StageFrontController::class.'@store')->middleware($middleware);

    });

}
