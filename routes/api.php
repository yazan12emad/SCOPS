<?php

use App\Http\Controllers\LoginUserController;
use App\Http\Controllers\RegisterUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterUserController::class, 'addNewUser']);

Route::post('/logIn', [LoginUserController::class, 'logIn']);
