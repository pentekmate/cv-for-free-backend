<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CVController;



Route::post('/login', [AuthController::class, 'login']);


Route::get('/me',[AuthController::class,'me']);


Route::middleware('auth:sanctum')->group(function(){
    Route::get('logout',[AuthController::class,'logout']);
    Route::post('createCv',[CVController::class,'createCv']);
});