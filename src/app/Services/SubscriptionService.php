<?php

namespace App\Services;

use App\Repositories\Eloquent\SubscriptionPlanRepository;
use App\Repositories\Eloquent\UserSubscriptionRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Models\Payment;
use Carbon\Carbon;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionPlanRepository $planRepository,
        protected UserSubscriptionRepository $subscriptionRepository,
        protected PaymentRepository $paymentRepository
    ) {}

    public function getAvailablePlans()
    {
        return $this->planRepository->getActivePlans();
    }

    public function getCurrentSubscription(int $userId)
    {
        return $this->subscriptionRepository->getActiveByUser($userId);
    }

    public function hasActiveSubscription(int $userId): bool
    {
        $subscription = $this->getCurrentSubscription($userId);
        return $subscription !== null;
    }

    public function activateSubscription(int $userId, int $planId, Payment $payment): UserSubscription
    {
        $plan = $this->planRepository->find($planId);
        
        if (!$plan) {
            throw new \Exception('Plan not found');
        }

        // End any existing active subscription
        $existingSubscription = $this->subscriptionRepository->getActiveByUser($userId);
        if ($existingSubscription) {
            $existingSubscription->update(['status' => 'cancelled']);
        }

        // Create new subscription
        $subscription = $this->subscriptionRepository->createSubscription(
            $userId,
            $planId,
            [
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays($plan->duration_days),
                'amount_paid' => $payment->amount,
                'payment_method' => $payment->payment_system,
            ]
        );

        // Link payment to subscription
        $payment->user_subscription_id = $subscription->id;
        $payment->save();

        return $subscription;
    }

    public function getSubscriptionStats(int $userId): array
    {
        $subscription = $this->getCurrentSubscription($userId);
        
        if (!$subscription) {
            return [
                'has_subscription' => false,
                'plan_name' => null,
                'ends_at' => null,
                'days_remaining' => 0,
                'status' => 'none',
            ];
        }

        return [
            'has_subscription' => true,
            'plan_name' => $subscription->plan->name ?? null,
            'ends_at' => $subscription->ends_at?->toIso8601String(),
            'days_remaining' => $subscription->daysRemaining(),
            'status' => $subscription->isActive() ? 'active' : 'expired',
        ];
    }
}
