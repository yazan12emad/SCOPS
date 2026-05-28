<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginUserValidation;
use App\Http\Requests\RegisterUserValidation;
use App\Services\AuthServices;
use App\Traits\ApiResponse;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuthServices $authService ){}

    public function register(RegisterUserValidation $request){
        try {
            $newUserData = $this->authService->register($request->validated());

            try {
                Mail::to($newUserData['user']->email)
                    ->queue(new WelcomeMail($newUserData['user']->first_name));
            } catch (\Exception $e) {
                \Log::error('Welcome email failed: ' . $e->getMessage());
            }

            return $this->jsonResponse([
                'message' => 'Account created successfully.',
                'success' => true,
                'token'   => $newUserData['token'],
                'user'    => $newUserData['user'],
            ], 201);

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

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->error('Email not found', 404);
        }

        // Generate a random verfying code
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                //'token' => Hash::make($token),// i think we should put this instead 'token' => $token,
                'token' => $token,
                'created_at' => now()
            ]
        );

        // Send email
        Mail::to($user->email)->send(
            new PasswordResetMail($user->first_name, $token)
        );

        return $this->success(null, 'Password reset link sent to your email');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        // Find token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return $this->error('Invalid or expired reset token', 400);
        }

        // Check token validity (60 minutes)
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
            return $this->error('Reset token has expired', 400);
        }

        // Verify token
        if ($request->token !== $record->token) {
            return $this->error('Invalid reset token', 400);
        }

        // Update password
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Delete used token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return $this->success(null, 'Password reset successfully');
    }
}
