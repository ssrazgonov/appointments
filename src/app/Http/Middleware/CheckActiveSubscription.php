<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        // Check if user has active subscription
        $subscription = $user->subscription;

        if (!$subscription || !$subscription->isActive()) {
            return response()->json([
                'message' => 'Active subscription required',
                'subscription' => $subscription ? [
                    'status' => $subscription->status,
                    'ends_at' => $subscription->ends_at?->toIso8601String(),
                ] : null,
            ], 403);
        }

        return $next($request);
    }
}
