<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function(){
    dd('Welcome to the API');
});


Route::post('/register', [AuthController::class, 'register']);

Route::post('/logIn', [AuthController::class, 'logIn'])
    ->middleware('prevent.auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [profileController::class, 'userProfile']);

    Route::put('/profile', [profileController::class, 'updateProfile']);

    Route::post('/profile/change-password', [profileController::class, 'changePassword']);

});
