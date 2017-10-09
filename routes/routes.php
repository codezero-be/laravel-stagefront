<?php

use CodeZero\StageFront\Controllers\StageFrontController;

if (config('stagefront.enabled') === true) {

    Route::group(['middleware' => config('stagefront.middleware')], function () {

        $url = config('stagefront.url');

        Route::get($url, StageFrontController::class.'@create');
        Route::post($url, StageFrontController::class.'@store');

    });

}
