<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function plans(): JsonResponse
    {
        $plans = $this->subscriptionService->getAvailablePlans();

        return response()->json([
            'plans' => $plans,
        ]);
    }

    public function current(): JsonResponse
    {
        $stats = $this->subscriptionService->getSubscriptionStats(auth()->id());

        return response()->json($stats);
    }

    public function hasActive(): JsonResponse
    {
        $hasActive = $this->subscriptionService->hasActiveSubscription(auth()->id());

        return response()->json([
            'has_active_subscription' => $hasActive,
        ]);
    }
}
