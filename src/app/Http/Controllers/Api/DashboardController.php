<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected SubscriptionService $subscriptionService
    ) {}

    public function index(): JsonResponse
    {
        $stats = $this->reportService->getDashboardStats(auth()->id());
        $subscription = $this->subscriptionService->getSubscriptionStats(auth()->id());

        return response()->json([
            'dashboard' => $stats,
            'subscription' => $subscription,
        ]);
    }
}
