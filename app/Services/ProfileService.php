<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function updateProfile(User $user, array $data): bool
    {
        if (!$user->update($data)) {
            throw new \Exception('Failed to update profile');
        }
        return $user->wasChanged();
    }

    public function changePassword(User $user , array $data)
    {
        if (!Hash::check($data['current_password'], $user->password)) {
                throw new \Exception('Failed to update change password');
        }

        $user->update(['password' => Hash::make($data['password'])]);
        return $user->wasChanged();
    }


}
