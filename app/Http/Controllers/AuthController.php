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
use App\Mail\EmailVerificationMail;


class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuthServices $authService ){}

    public function register(RegisterUserValidation $request){
        try {
            $newUserData = $this->authService->register($request->validated());

            //generate code
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            //saved teh code for the comparions
            $newUserData['user']->update([
                'email_verification_code' => $code
            ]);

            //send a verfiy email with the saved code
            try {
                Mail::to($newUserData['user']->email)->send(
                    new EmailVerificationMail($newUserData['user']->first_name, $code)
                );
            } catch (\Exception $e) {
                \Log::error('Verification email failed: ' . $e->getMessage());
            }

            return $this->jsonResponse([
                'message' => 'Account created. Please verify your email.',
                'success' => true,
                'user'    => $newUserData['user'],
            ], 201);

        } catch (\Exception $exception) {
            return $this->jsonResponse([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->error('Email not found. Please register first.', 404);
        }

        if ($user->email_verified_at) {
            return $this->success(null, 'Email already verified. Please login.');
        }

        if ($user->email_verification_code !== $request->code) {
            return $this->error('Invalid verification code.', 400);
        }

        $user->update([
            'email_verified_at'       => now(),
            'email_verification_code' => null
        ]);

        //send a  welcomeing email after the verification
        try {
            Mail::to($user->email)->send(
                new WelcomeMail($user->first_name)
            );
        } catch (\Exception $e) {
            \Log::error('Welcome email failed: ' . $e->getMessage());
        }

        return $this->success(null, 'Email verified successfully. You can now login!');
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

        //find the token
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return $this->error('Invalid or expired reset token', 400);
        }

        //check token validity 60 minutes if it is older delete it
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();// this line delete the token
            return $this->error('Reset token has expired', 400);
        }

        //verify the token
        if ($request->token !== $record->token) {
            return $this->error('Invalid reset token', 400);
        }

        //update password
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        //delete used token after successfully reset the passwrod
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return $this->success(null, 'Password reset successfully');
    }
}
