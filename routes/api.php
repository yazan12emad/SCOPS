<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;


    Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the API',
    ]);
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logIn', [AuthController::class, 'logIn'])
        ->middleware('prevent.auth.login');

    Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'userProfile']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::get('/services', [ServiceController::class, 'index']);

    Route::get('/services/{id}', [ServiceController::class, 'show']);

    Route::get('/cards', [CardController::class, 'getCards']);
    Route::post('/cards', [CardController::class, 'addCard']);
    Route::delete('/cards/{card}', [CardController::class, 'deleteCard']);
    Route::patch('/cards/{card}/primary', [CardController::class, 'changePrimary']);
});
