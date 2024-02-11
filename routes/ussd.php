<?php

use App\Http\Controllers\USSDController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], fn() => Route::post("/", USSDController::class));
