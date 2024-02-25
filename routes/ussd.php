<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\USSDController;
use App\Http\Controllers\AgentUSSDController;

Route::group(['prefix' => 'v1'], fn() => Route::post("/", USSDController::class));
Route::group(['prefix' => 'v2'], fn() => Route::post("/", AgentUSSDController::class));
