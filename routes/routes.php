<?php

use CodeZero\StageFront\Controllers\StageFrontController;

if (config('stagefront.enabled') === true) {

    Route::group(['middleware' => config('stagefront.middleware')], function () {

        $url = config('stagefront.url');
        $throttle = config('stagefront.throttle');
        $tries = config('stagefront.throttle_tries');
        $delay = config('stagefront.throttle_delay');
        $middleware = $throttle ? "throttle:{$tries},{$delay}" : [];

        Route::get($url, StageFrontController::class.'@create');
        Route::post($url, StageFrontController::class.'@store')->middleware($middleware);

    });

}
