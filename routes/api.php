<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CVController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:global'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/tiers', [SubscriptionController::class, 'index']);
    Route::get('/templates', [TemplateController::class, 'index']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/createCv', [CVController::class, 'createCv']);
        Route::get('/userCVs', [CVController::class, 'index']); // ✅
        Route::post('/userCVs/update', [CVController::class, 'update']);
        // Route::get('/userCVs/show',[CVController::class,'show']);
        Route::post('/deleteCV', [CVController::class, 'delete']);
    });

    Route::post('/regist', [AuthController::class, 'regist']);
});

// Route::get('userCVs/{cv_id}', [CVController::class, 'show']);

Route::get('/image/{filename}', function ($filename) {
    $path = storage_path('app/public/cv-images/'.$filename);

    if (! file_exists($path)) {
        abort(404);
    }

    $mimeType = mime_content_type($path);
    $contents = file_get_contents($path);

    return Response::make($contents, 200, [
        'Content-Type' => $mimeType,
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
    ]);
});
