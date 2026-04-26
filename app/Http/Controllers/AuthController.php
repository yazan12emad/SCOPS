<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserValidation;
use App\Http\Requests\registerUserValidation;
use App\Services\AuthServices;

class AuthController extends Controller
{
    public function __construct(private AuthServices $authService ){}

    public function register(registerUserValidation $request){
        try {
            $newUserData = $this->authService->register($request->validated());

            if(!$newUserData){
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Registration failed , please try again',
                ], 500);
            }

            return $this->jsonResponse([
                'message' => 'Account created successfully.',
                'success' => true,
                'token'   => $newUserData['token'],
                'user'    => $newUserData['user'],

            ] , 201);

        } catch (\Exception $exception) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Registration failed , please try again',
            ], 500);
        }

    }

    public function login(loginUserValidation $request){
        try {
            $result = $this->authService->logIn($request->validated());

            return $this->jsonResponse([
                'success' => true,
                'message' => 'Logged in successfully.',
                'token' => $result['token'],
                'user' => $result['user'],
            ], 200);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return $this->jsonResponse([
            'success' => true,
            'message' => 'Logged out successfully.',
        ], 200);
    }
}
