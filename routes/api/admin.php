<?php

use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminUserController;

Route::group(['middleware' => ['check.admin']], function () {
    Route::resource('users', AdminUserController::class)->only('index', 'show', 'update');
    Route::resource('transactions', AdminTransactionController::class)->only('index', 'show');
});
