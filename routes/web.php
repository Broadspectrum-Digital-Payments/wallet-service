<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return successfulResponse([
        'api' => 'Wallet Service',
        'version' => '1.0.0'
    ]);
});
