<?php

namespace App\Services;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Role; // Make sure Role model is imported
use App\Services\Interfaces\AuthServiceInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
class AuthService implements AuthServiceInterface
{

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $roleName = $request->role ?? 'customer';
        $roleModel = Role::firstWhere('name', $roleName);

        if ($roleModel) {
            $user->roles()->attach($roleModel);
            Log::info('User created and role attached:', ['user_id' => $user->id, 'role' => $roleName]);
        } else {
            return response()->json(['error' => 'Role not found'], 400);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'created_at', 'updated_at']),
            'token' => $token,
        ], 201);
    }

    // public function login(LoginRequest $request): JsonResponse
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (!$token = auth('api')->attempt($credentials)) {
    //         $error = User::where('email', $request->email)->exists() ? 'Incorrect password.' : 'Email not registered.';
    //         return response()->json(['error' => $error], 401);
    //     }

    //     $user = auth('api')->user();

    //     return response()->json([
    //         'user' => $user->only(['id', 'name', 'email', 'created_at', 'updated_at']),
    //         'token' => $token,
    //     ]);
    // }



    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $email = $request->email;
        $cacheKey = 'login_attempts:' . $email;
        $blockTime = 120; // Block time in seconds
        $maxAttempts = 5; // Maximum number of allowed attempts

        // Fetch login attempts from cache
        $loginAttempts = Cache::get($cacheKey, ['attempts' => 0, 'blocked_until' => null]);

        // Check if the user is blocked
        if ($loginAttempts['blocked_until'] && Carbon::now()->lessThan(Carbon::createFromTimestamp($loginAttempts['blocked_until']))) {
            $remainingTime = Carbon::now()->diffInSeconds(Carbon::createFromTimestamp($loginAttempts['blocked_until']));
            return response()->json(['error' => "Too many failed attempts. Try again in {$remainingTime} seconds."], 403);
        }

        // Attempt login
        if (!$token = auth('api')->attempt($credentials)) {
            $error = User::where('email', $email)->exists() ? 'Incorrect password.' : 'Email not registered.';

            // Update login attempts
            $attempts = $loginAttempts['attempts'] + 1;
            $blockedUntil = $attempts >= $maxAttempts ? Carbon::now()->addSeconds($blockTime)->timestamp : null;
            Cache::put($cacheKey, ['attempts' => $attempts, 'blocked_until' => $blockedUntil], $blockTime);

            // Calculate remaining attempts
            $remainingAttempts = $maxAttempts - $attempts;
            $remainingAttemptsMessage = $remainingAttempts > 0
                ? "You have {$remainingAttempts} remaining attempt(s)."
                : "Your account is blocked. Try again in {$blockTime} seconds.";

            return response()->json(['error' => $error, 'message' => $remainingAttemptsMessage], 401);
        }

        // Successful login
        $user = auth('api')->user();

        // Reset login attempts
        Cache::forget($cacheKey);

        return response()->json([
            'user' => $user->only(['id', 'name', 'email', 'created_at', 'updated_at']),
            'token' => $token,
        ]);
    }
    public function getUser(): JsonResponse
    {
        $user = auth('api')->user()->load('roles.permissions');
        return response()->json($user);
    }
    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
