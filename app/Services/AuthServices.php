<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\ActivityLogService;

class AuthServices
{
    public function register(array $data): array
    {
            if(!$user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'], // hash by the User Model
                'phone' => $data['phone'],
            ])){
                throw new \Exception("Failed to create user");
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return [
                'success' => true,
                'token' => $token,
                'user' => $user,
            ];
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
        $user->logActivity('login', [
            'IP' => request()->ip(),
            'device' => request()->userAgent(),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'success' => true,
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logOut(): void
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception("User not found");
        }
        $user->logActivity('logout', [
            'IP' => request()->ip(),
        ]);
        $user->tokens()->delete();
    }


}
