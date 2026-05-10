<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordValidation;
use App\Http\Requests\ProfileUpdateValidation;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService){}

    public function userProfile(request $request)
    {
        return $this->jsonResponse([
            'user' => $this->profileService->getProfile(),
        ]);
    }

    public function updateProfile(ProfileUpdateValidation $request){
        try {
            $user = auth()->user();
            $wasChanged = $this->profileService->updateProfile($user, $request->validated());
            return $this->jsonResponse([
                'success' => $wasChanged,
                'message' => $wasChanged ? 'Profile updated successfully' : 'Nothing to update',
            ]);
        }
        catch (\Exception $exception){
            return $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ] , 500);
        }
    }

    public function changePassword(ChangePasswordValidation $request){
        try {
            $user = auth()->user();
            $wasChanged =  $this->profileService->changePassword($user , $request->validated());
            return $this->jsonResponse([
                'success' => $wasChanged,
                'message' => $wasChanged ? 'Password updated successfully' : 'Nothing to update',
            ]);
        }
        catch (\Exception $exception){
            return $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ] , 500);
        }
    }


}
