<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClientAccount;
use App\Models\MasterProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        // Check if account exists
        $client = ClientAccount::where('phone', $request->phone)->first();

        if ($client) {
            return response()->json([
                'message' => 'Account with this phone already exists',
            ], 422);
        }

        // Create client account
        $client = ClientAccount::create([
            'phone' => $request->phone,
            'email' => $request->email,
            'name' => $request->name,
            'password' => $request->password ? Hash::make($request->password) : null,
            'is_verified' => true, // Auto-verify for now
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($client);

        return response()->json([
            'message' => 'Registration successful',
            'client' => $client,
            'token' => $token,
            'token_type' => 'bearer',
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'nullable|string',
            'verification_code' => 'nullable|string',
        ]);

        $client = ClientAccount::where('phone', $request->phone)->first();

        if (!$client) {
            return response()->json([
                'message' => 'Account not found. Please register first.',
            ], 404);
        }

        // Check password or verification code
        $authenticated = false;

        if ($request->password && $client->password) {
            $authenticated = Hash::check($request->password, $client->password);
        } elseif ($request->verification_code) {
            $authenticated = $client->verifyCode($request->verification_code);
        }

        if (!$authenticated) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Generate JWT token
        $token = JWTAuth::fromUser($client);

        return response()->json([
            'message' => 'Login successful',
            'client' => $client,
            'token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function sendVerificationCode(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:20',
        ]);

        $client = ClientAccount::where('phone', $request->phone)->first();

        if (!$client) {
            return response()->json([
                'message' => 'Account not found',
            ], 404);
        }

        $code = $client->generateVerificationCode();

        // Here you would send SMS
        // For now, just return it for testing
        return response()->json([
            'message' => 'Verification code sent',
            'code' => $code, // Remove in production
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'client' => auth()->user(),
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
