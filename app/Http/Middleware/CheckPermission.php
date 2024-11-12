<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            // If user is not authenticated, return a 401 Unauthorized response
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        Log::info('Authenticated user:', ['user' => $user->id]);

        $user->load('roles.permissions');

        $permissions = $user->roles->flatMap(function ($role) {
            return $role->permissions->pluck('name');
        })->unique();

        Log::info('User permissions:', ['permissions' => $permissions->toArray()]);

        if (!$permissions->contains($permission)) {
            return response()->json(['message' => "You don't have permission to access this resource."], 403);
        }

        return $next($request);
    }
    
}
