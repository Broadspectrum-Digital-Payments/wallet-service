<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminTransactionController;

Route::group(['middleware' => ['check.admin']], function () {
    Route::get('dashboard', DashboardController::class)
    ;
    Route::get('/users/{externalId}/transactions', [AdminUserController::class, 'transactions']);
    Route::resource('users', AdminUserController::class)->only('index', 'show', 'update');
    Route::post('/users/{user}/transactions', [AdminUserController::class, 'performTransaction']);

    Route::resource('transactions', AdminTransactionController::class)->only('index', 'show');

    Route::get('reports/loans/download', [ReportController::class, 'loanReport']);
    Route::get('reports/transactions/download', [ReportController::class, 'transactionReport']);
});
