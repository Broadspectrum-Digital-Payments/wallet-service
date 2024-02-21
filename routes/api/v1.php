<?php

use App\Http\Controllers\MerchantController;
use App\Http\Controllers\UserTransactionController;

Route::group(['prefix' => 'users'], function () {
    Route::controller('App\Http\Controllers\UserController')->group(function () {
        Route::post("/otp", 'sendOTP');
        Route::post('/otp/verify', 'verifyOTP');
        Route::post("/login", 'login');
        Route::post("/register", 'register');
        Route::get("/name-enquiry", 'nameEnquiry');
        Route::put("/change-pin", 'changePIN');

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

Route::post("/merchants/register", [MerchantController::class, 'register']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('transactions', UserTransactionController::class)->only('index', 'store', 'show');
});
