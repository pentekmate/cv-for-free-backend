<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CVController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

Route::middleware(['throttle:global'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/templates', [TemplateController::class, 'index']);
    Route::post('/createCv', [CVController::class, 'createCv']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/logout', [AuthController::class, 'logout']);
    });

    // Route::get('userCVs/{cv_id}', [CVController::class, 'show']);
    Route::get('/userCVs', [CVController::class, 'index']);
    
    Route::post('/userCVs/update', [CVController::class, 'update']);
});







Route::get('/image/{filename}', function ($filename) {
    $path = storage_path('app/public/cv-images/' . $filename);

    if (!file_exists($path)) {
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



