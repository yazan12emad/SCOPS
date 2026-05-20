<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function(){
    dd('Welcome to the API');
});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logIn', [AuthController::class, 'logIn'])->middleware('prevent.auth.login');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);// the id here is for the service

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [profileController::class, 'userProfile']);
    Route::put('/profile', [profileController::class, 'updateProfile']);
    Route::post('/profile/change-password', [profileController::class, 'changePassword']);

    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::put('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel']);
    Route::get('/subscriptions/{id}/renewal', [SubscriptionController::class, 'calculateNextRenewal']);
    Route::put('/subscriptions/{id}/pause', [SubscriptionController::class, 'pause']);
    Route::put('/subscriptions/{id}/resume', [SubscriptionController::class, 'resume']);
    Route::get('/financial-summary', [SubscriptionController::class, 'financialSummary']);


    Route::get('/cards', [CardController::class, 'getCards']);
    Route::post('/cards', [CardController::class, 'addCard']);
    Route::delete('/cards/{card}', [CardController::class, 'deleteCard']);
    Route::patch('/cards/{card}/primary', [CardController::class, 'changePrimary']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Reviews
    Route::get('/reviews/{service_id}', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);
});

// Admin routes (auth:sanctum only, no admin middleware)
Route::middleware('auth:sanctum')->group(function () {
    //----------------------------------------------------USERS----------------------------------------------------------
    Route::get('/admin/users', [AdminController::class, 'listUsers']);
    Route::post('/admin/users', [AdminController::class, 'addUser']);
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
    Route::put('/admin/users/{id}/toggle', [AdminController::class, 'toggleUser']);
    Route::put('/admin/users/{id}/reset-password', [AdminController::class, 'resetPassword']);
    //----------------------------------------------------SUPSCRIPTIONS----------------------------------------------------------
    Route::get('/admin/users/{id}/subscriptions', [AdminController::class, 'userSubscriptions']);
    //----------------------------------------------------SERVICES----------------------------------------------------------
    Route::get('/admin/services', [AdminController::class, 'listServices']);
    Route::post('/admin/services', [AdminController::class, 'addService']);
    Route::put('/admin/services/{id}', [AdminController::class, 'updateService']);
    Route::delete('/admin/services/{id}', [AdminController::class, 'deleteService']);
    //----------------------------------------------------STATISTICS--------------------------------------------------------
    Route::get('/admin/statistics', [AdminController::class, 'statistics']);
});

Route::get('/run-reminders', function () {
    Artisan::call('reminders:send');
    return 'Reminders sent: ' . now();
});

Route::get('/run-reminders/{token}', function ($token) {
    if ($token !== config('app.reminder_token')) {
        abort(403);
    }
    Artisan::call('reminders:send');
    return 'Reminders sent: ' . now();
});

