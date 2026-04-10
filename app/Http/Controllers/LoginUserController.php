<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserValidation;
use Illuminate\Http\Request;
use App\Services\AuthServices;

class LoginUserController extends Controller
{
    public function __construct(private AuthServices $authService)
    {
    }
    // fake data for test the logIn methods
//{
//"email": "yazan@example.com",
//"password": "password123"
//}

    public function login(LoginUserValidation $request)
    {
        try {
            $result = $this->authService->logIn($request->validated());
            if (!$result['success']) {
                return $this->jsonResponse([
                    'success' => false,
                    'message' => $result['message']
                ], 401);
            }

            return $this->jsonResponse([
                'success' => true,
                'token' => $result['token'],
                'user' => $result,
            ], 200);

        } catch (\Exception $e) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Login failed, please try again.',
            ], 500);
        }
    }


}
