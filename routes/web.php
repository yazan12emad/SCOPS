<?php

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Route;


Route::get('/', function(){
    $data = [
        'users'        => Cache::remember('count_users', 300, fn() => User::count()),
        'payment'      => Cache::remember('count_payments', 300, fn() => Payment::count()),
        'subscription' => Cache::remember('count_subscriptions', 300, fn() => subscription::count()),
    ];

    return response()->view('welcome_page', $data)
        ->header('Cache-Control', 'public, max-age=60');
});
