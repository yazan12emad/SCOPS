<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function(){
   return view('welcome_page' ,[
       'users' => User::count() ,
        'payment' => Payment::count() ,
       'subscription' => subscription::count() ,
   ]);
});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logIn', [AuthController::class, 'logIn'])->middleware('prevent.auth.login');

Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);

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

    Route::post('/cards/setup-intent', [CardController::class, 'createSetupIntent']);
    Route::post('/cards', [CardController::class, 'agit ddCard']);
    Route::get('/cards', [CardController::class, 'getCards']);
    Route::delete('/cards/{card}', [CardController::class, 'deleteCard']);
    Route::patch('/cards/{card}/primary', [CardController::class, 'changePrimary']);

    //Payments process
    Route::post('/payments/{service}', [PaymentController::class, 'MakePayment']);
    Route::Post('/payments/confirm/{payment}', [PaymentController::class, 'confirmPayment']);
    Route::get('/payments/{payment}',  [PaymentController::class, 'showPayment']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // Reviews
    Route::get('/reviews/{service_id}', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);

// Admin routes (auth:sanctum only, no admin middleware)
// no need to add new group if we didn't make any middlewares for the admin
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

// NO auth middleware — Stripe signs this itself
Route::post('/webhook', [PaymentController::class, 'webhook']);

Route::get('/run-reminders', function () {
    Artisan::call('reminders:send');
    return 'Reminders sent: ' . now();
});

Route::get('/run-reminders/{token}', function ($token) {
    if ($token !== config('app.reminder_token')) {
        abort(403);
    }
    $targetDate = now()->addDays(3)->toDateString();
    $count = Subscription::where('status', 'active')
        ->whereDate('renewal_date', $targetDate)
        ->count();
    Artisan::call('reminders:send');
    return 'Target date: ' . $targetDate .
        ' | Found: ' . $count .
        ' | Now: ' . now();
});

Route::get('/ping', function () {
    return 'pong-v2';
});


Route::get('/receipt' , function(){
    return view('pdf.payment_receipt' ,[

    ]);
});
