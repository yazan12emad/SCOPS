<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubscriptionController;
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


    // this return all the subscriptions for the logged in user(GET request to /subscriptions)
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    // create a new subscription (POST request to /subscriptions)
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    //(PUT request to /subscriptions/{id}/cancel) → cancels subscription with id of the user
    Route::put('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel']);
    //(Get request to /subscriptions/{id}/renewal) → calculates next renewal date for subscription with id of the user
    Route::get('/subscriptions/{id}/renewal', [SubscriptionController::class, 'calculateNextRenewal']);

//GET /categories → returns all categories (Streaming, Music, etc.)
Route::get('/categories', [CategoryController::class, 'index']);
//GET /services → returns all services (Netflix, Spotify, etc.)
Route::get('/services', [ServiceController::class, 'index']);
//GET /services/3 → returns one specific service by its id (and we used show because it is returns one record by ID)
Route::get('/services/{id}', [ServiceController::class, 'show']);
//(PUT request to /subscriptions/{id}/pause) → pauses subscription with the given id
Route::put('/subscriptions/{id}/pause', [SubscriptionController::class, 'pause']);
//(PUT request to /subscriptions/{id}/resume) → resumes a paused subscription with the given id
Route::put('/subscriptions/{id}/resume', [SubscriptionController::class, 'resume']);

    Route::get('/cards', [CardController::class, 'getCards']);
    Route::post('/cards', [CardController::class, 'addCard']);
    Route::delete('/cards/{card}', [CardController::class, 'deleteCard']);
    Route::patch('/cards/{card}/primary', [CardController::class, 'changePrimary']);
});
