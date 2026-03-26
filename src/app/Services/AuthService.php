<?php

namespace App\Services;

use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\UserSubscriptionRepository;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserSubscriptionRepository $subscriptionRepository
    ) {}

    public function register(array $data): array
    {
        $user = $this->userRepository->create($data);
        
        // Create default notification settings
        NotificationSetting::create([
            'user_id' => $user->id,
        ]);

        // Create 7-day trial subscription
        $this->createTrialSubscription($user);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $credentials): array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new \Exception('Invalid credentials', 401);
        }

        $user = Auth::user();
        
        if (!$user->is_active) {
            JWTAuth::invalidate($token);
            throw new \Exception('Account is deactivated', 403);
        }

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function refresh(): array
    {
        return [
            'token' => JWTAuth::refresh(JWTAuth::getToken()),
            'user' => Auth::user(),
        ];
    }

    public function me()
    {
        return Auth::user();
    }

    protected function createTrialSubscription($user): void
    {
        $this->subscriptionRepository->createSubscription(
            $user->id,
            1, // Default plan ID (will be created by seeder)
            [
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays(7),
                'trial_ends_at' => now()->addDays(7),
                'amount_paid' => 0,
            ]
        );
    }
}
