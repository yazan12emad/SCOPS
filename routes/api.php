<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;


Route::get('/', function(){
    dd('Welcome to the API');
});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logIn', [AuthController::class, 'logIn'])->middleware('prevent.auth.login');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [profileController::class, 'userProfile']);

    Route::put('/profile', [profileController::class, 'updateProfile']);

    Route::post('/profile/change-password', [profileController::class, 'changePassword']);
});

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/services', [ServiceController::class, 'index']);

Route::get('/services/{id}', [ServiceController::class, 'show']);
