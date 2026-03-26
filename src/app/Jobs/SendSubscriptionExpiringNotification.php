<?php

namespace App\Jobs;

use App\Models\UserSubscription;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSubscriptionExpiringNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected UserSubscription $subscription
    ) {}

    public function handle(): void
    {
        $user = $this->subscription->user;
        $settings = $user->notificationSetting;

        if (!$settings || !$settings->email_subscription_expiring) {
            return;
        }

        $daysRemaining = $this->subscription->daysRemaining();

        Log::info('Sending subscription expiring notification', [
            'user_id' => $user->id,
            'email' => $user->email,
            'days_remaining' => $daysRemaining,
        ]);

        // Here you would send an actual email
        // $user->notify(new SubscriptionExpiringNotification($daysRemaining));
    }
}
