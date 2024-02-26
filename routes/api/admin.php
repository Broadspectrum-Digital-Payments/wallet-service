<?php

use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\AdminUserController;

Route::group(['middleware' => ['check.admin']], function () {
    Route::get('/users/{externalId}/transactions', [AdminUserController::class, 'transactions']);
    Route::resource('users', AdminUserController::class)->only('index', 'show', 'update');
    Route::post('/users/{user}/transactions', [AdminUserController::class, 'performTransaction']);

    Route::resource('transactions', AdminTransactionController::class)->only('index', 'show');
});
