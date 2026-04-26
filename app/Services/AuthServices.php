<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthServices
{
    public function register(array $data): array|bool
    {
        try {
           $user =  User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'success' => true,
                'token' => $token,
                'user' => $user,
            ];
        }
        catch (\Exception $exception){
            return false;
        }

    }

  public function logIn(array $userData): array
  {
        $user = User::where('email', $userData['email'])->first();

        if (!$user) {
            throw new \Exception("User not found");
        }

        if (!Hash::check($userData['password'], $user->password)) {
            throw new \Exception("password not correct");
        }

      if (!$user->is_active) {
          throw new \Exception('Your account is deactivated.');
      }

      auth()->login($user);

      $token = $user->createToken('auth_token')->plainTextToken;

      return [
          'success' => true,
          'user'    => $user,
          'token'   => $token,
      ];

  }


}
