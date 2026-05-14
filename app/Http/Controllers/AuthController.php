<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserValidation;
use App\Http\Requests\RegisterUserValidation;
use App\Services\AuthServices;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuthServices $authService ){}

    public function register(RegisterUserValidation $request){
        try {
            $newUserData = $this->authService->register($request->validated());
            return $this->jsonResponse([
                'message' => 'Account created successfully.',
                'success' => true,
                'token'   => $newUserData['token'],
                'user'    => $newUserData['user'],
            ] , 201);


        } catch (\Exception $exception) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
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

        } catch (\Exception $exception) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 401);
        }
    }

    public function logout()
    {
        try{
          $this->authService->logOut();
        return $this->jsonResponse([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
        }
        catch (\Exception $exception){
            return $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
