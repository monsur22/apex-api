<?php

namespace App\Services\Interfaces;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;

interface AuthServiceInterface
{
    public function register(RegisterRequest $request): JsonResponse;
    public function login(LoginRequest $request): JsonResponse;
    public function getUser(): JsonResponse;
    public function logout(): JsonResponse;
    public function refresh(): JsonResponse;
}
