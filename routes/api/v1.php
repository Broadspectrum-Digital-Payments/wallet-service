<?php

Route::group(['prefix' => 'users'], function () {
    Route::controller('App\Http\Controllers\UserController')->group(function () {
        Route::post("/otp", 'sendOTP');
        Route::post('/otp/verify', 'verifyOTP');
        Route::post("/login", 'login');
        Route::post("/register", 'register');
        Route::get("/name-enquiry", 'nameEnquiry');

        // Authenticated Routes
        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get("/me", 'me');
            Route::group(['prefix' => 'kyc'], function () {
                Route::get("/docs", 'docs');
                Route::post("/", 'kyc');
            });
        });
    });
});
