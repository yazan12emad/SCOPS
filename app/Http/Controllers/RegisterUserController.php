<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationValidation;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\UserService;

class RegisterUserController extends Controller
{

    // fake data for test the registration methods
//{
//"username": "yazan",
//"email": "yazan@example.com",
//"password": "password123",
//"phone": "0791234567"
//}

    public function __construct(private UserService $userService){}

    public function addNewUser(UserRegistrationValidation $request)
    {
        try {
            $newUser = $this->userService->createUser($request->validated());

            if(!$newUser){
                return $this->jsonResponse([
                    'success' => false,
                    'message' => 'Registration failed , please try again',
                ], 500);
            }

            return $this->jsonResponse([
                'success' => true,
                'User_data' => [
                    'id' => $newUser->user_id,
                    'User_name' => $newUser->username,
                    'email' => $newUser->email,
                    'phone' => $newUser->phone,
                ],
            ] , 201);

        } catch (\Exception $exception) {
            return $this->jsonResponse([
                'success' => false,
                'message' => 'Registration failed , please try again',
                ], 500);
        }
    }


}
