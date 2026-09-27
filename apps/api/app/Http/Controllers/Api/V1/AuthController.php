<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Notifications\SendActivationCodeNotification;
use App\Notifications\SendPasswordResetCodeNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user and dispatch activation code email.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $activationCode = (string) random_int(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
            'activation_code' => $activationCode,
            'activation_code_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        $user->notify(new SendActivationCodeNotification($activationCode));

        return $this->successResponse(
            [
                'user' => new UserResource($user),
            ],
            'Registration successful. Please check your email for the account activation code.',
            201
        );
    }

    /**
     * Activate user account via verification code.
     */
    public function activate(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (
            ! $user->activation_code ||
            $user->activation_code !== $request->code ||
            Carbon::now()->greaterThan($user->activation_code_expires_at)
        ) {
            throw ValidationException::withMessages([
                'code' => ['The activation code is invalid or has expired.'],
            ]);
        }

        $user->update([
            'status' => 'active',
            'email_verified_at' => Carbon::now(),
            'activation_code' => null,
            'activation_code_expires_at' => null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            [
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            'Account successfully activated.'
        );
    }

    /**
     * Login user and check account status.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Your account is not active. Please verify your activation code first.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            [
                'user' => new UserResource($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            'Logged in successfully.'
        );
    }

    /**
     * Request a password reset code.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $resetCode = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'code' => $resetCode,
                'expires_at' => Carbon::now()->addMinutes(15),
                'created_at' => Carbon::now(),
            ]
        );

        $user = User::where('email', $request->email)->first();
        $user->notify(new SendPasswordResetCodeNotification($resetCode));

        return $this->successResponse(
            null,
            'Password reset code sent to your email.'
        );
    }

    /**
     * Reset password using validation code.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (! $record || Carbon::now()->greaterThan(Carbon::parse($record->expires_at))) {
            throw ValidationException::withMessages([
                'code' => ['The reset code is invalid or has expired.'],
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return $this->successResponse(
            null,
            'Password has been successfully reset.'
        );
    }

    /**
     * Get the authenticated user's information.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            'User profile retrieved successfully.'
        );
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            null,
            'Successfully logged out.'
        );
    }
}