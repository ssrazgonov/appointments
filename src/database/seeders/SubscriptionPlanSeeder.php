<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for beginners just starting their business',
                'price' => 990,
                'currency' => 'RUB',
                'duration_days' => 30,
                'max_clients' => 50,
                'max_appointments_per_day' => 10,
                'is_active' => true,
                'features' => [
                    'Up to 50 clients',
                    'Up to 10 appointments per day',
                    'Basic reports',
                    'Email support',
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'For growing businesses with more clients',
                'price' => 1990,
                'currency' => 'RUB',
                'duration_days' => 30,
                'max_clients' => 200,
                'max_appointments_per_day' => 30,
                'is_active' => true,
                'features' => [
                    'Up to 200 clients',
                    'Up to 30 appointments per day',
                    'Advanced reports',
                    'SMS reminders',
                    'Priority support',
                ],
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Maximum features for established businesses',
                'price' => 3990,
                'currency' => 'RUB',
                'duration_days' => 30,
                'max_clients' => 1000,
                'max_appointments_per_day' => 100,
                'is_active' => true,
                'features' => [
                    'Unlimited clients',
                    'Up to 100 appointments per day',
                    'Full analytics',
                    'SMS & Email reminders',
                    'API access',
                    '24/7 support',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
