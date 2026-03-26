<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return response()->json([
                'message' => 'Registration successful',
                'user' => $result['user'],
                'token' => $result['token'],
                'token_type' => 'bearer',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return response()->json([
                'message' => 'Login successful',
                'user' => $result['user'],
                'token' => $result['token'],
                'token_type' => 'bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authentication failed',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh(): JsonResponse
    {
        try {
            $result = $this->authService->refresh();

            return response()->json([
                'user' => $result['user'],
                'token' => $result['token'],
                'token_type' => 'bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token refresh failed',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'user' => $this->authService->me(),
        ]);
    }
}
