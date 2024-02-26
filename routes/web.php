<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return successfulResponse([
        'api' => 'Wallet Service',
        'version' => '1.0.0'
    ]);
});

Route::prefix('webhooks')->group(function () {
    Route::put('/middleware', [WebhookController::class, 'middlewareCallback'])->name('middleware.callback');
});
