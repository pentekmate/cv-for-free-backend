<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CVController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:global'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/templates',[TemplateController::class,'index']);
    Route::post('/createCv', [CVController::class, 'createCv']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/userCVs', [CVController::class, 'index']);
        Route::get('/logout', [AuthController::class, 'logout']);
    });

});
