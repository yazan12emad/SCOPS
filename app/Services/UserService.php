<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function createUser(array $data):user|false
    {
        try {
            User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
            ]);
        }
        catch (\Exception $exception){
            return false;
        }
         return User::where('email', $data['email'])->first();

    }
}
