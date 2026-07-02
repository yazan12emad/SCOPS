<?php

use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome_page', [
        'users'        => User::where('is_active', true)->count(),
        'subscription' => Subscription::where('status', 'active')->count(),
        'payment'      => Payment::where('status', 'successful')->count() . ' payments',
    ]);
});
