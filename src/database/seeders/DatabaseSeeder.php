<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run subscription plan seeder
        $this->call([
            SubscriptionPlanSeeder::class,
        ]);

        // Create test user
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'phone' => '+79991234567',
                'business_name' => 'Test Business',
                'business_type' => 'beauty',
                'is_active' => true,
            ]
        );

        // Create another test user
        User::updateOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('demo1234'),
                'phone' => '+79997654321',
                'business_name' => 'Demo Salon',
                'business_type' => 'beauty',
                'is_active' => true,
            ]
        );
    }
}
