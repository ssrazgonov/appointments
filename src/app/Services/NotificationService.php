<?php

namespace App\Services;

use App\Repositories\Eloquent\UserSubscriptionRepository;
use App\Models\User;
use App\Jobs\SendSubscriptionExpiringNotification;
use App\Jobs\SendAppointmentReminderNotification;

class NotificationService
{
    public function __construct(
        protected UserSubscriptionRepository $subscriptionRepository
    ) {}

    public function checkExpiringSubscriptions(): void
    {
        $expiringSubscriptions = $this->subscriptionRepository->getExpiringSoon(3);

        foreach ($expiringSubscriptions as $subscription) {
            dispatch(new SendSubscriptionExpiringNotification($subscription));
        }
    }

    public function checkAppointmentReminders(): void
    {
        // This would be called by a scheduled task
        // Implementation depends on specific requirements
    }

    public function sendWelcomeNotification(User $user): void
    {
        // Send welcome email/notification
    }

    public function sendNewClientNotification(User $user, $client): void
    {
        // Send notification about new client
    }

    public function sendPaymentReceivedNotification(User $user, $payment): void
    {
        // Send notification about payment received
    }
}
