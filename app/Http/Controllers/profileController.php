<?php

namespace App\Http\Controllers;

use App\Http\Requests\changePasswordValidation;
use App\Http\Requests\ProfileUpdateValidation;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class profileController extends Controller
{

    public function __construct(private ProfileService $profileService)
    {}

    public function userProfile(request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function updateProfile(ProfileUpdateValidation $request){

        try {
            $user = auth()->user();
            $wasChanged = $this->profileService->updateProfile($user, $request->validated());
            return response()->json([
                'success' => $wasChanged,
                'message' => $wasChanged ? 'Profile updated successfully' : 'Failed to update profile',
            ]);
        }

        catch (\Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ] , 500);
        }
    }

    public function changePassword(changePasswordValidation $request){
        try {
            $user = auth()->user();
            $wasChanged =  $this->profileService->changePassword($user , $request->validated());
            return response()->json([
                'success' => $wasChanged,
                'message' => $wasChanged ? 'Password updated successfully' : 'Failed to update password',
            ]);
        }
        catch (\Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ] , 500);
        }
    }


}
