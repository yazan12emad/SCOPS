<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthServices
{
  public function logIn(array $userData): array
  {
      $user = User::where('email', $userData['email'])->first();

      if (!$user || !Hash::check($userData['password'], $user->password)) {
          return [
              'success' => false,
              'message' => 'Invalid email or password',
          ];
      }

      $token = $user->createToken('logIn_token')->plainTextToken;
      return [
          'success' => true,
          'user'    => $user,
          'token'   => $token,
      ];

  }


}
