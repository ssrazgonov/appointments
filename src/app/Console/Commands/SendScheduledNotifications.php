<?php

namespace App\Console\Commands;

use App\Jobs\SendSubscriptionExpiringNotification;
use App\Jobs\SendAppointmentReminderNotification;
use App\Models\UserSubscription;
use App\Models\Appointment;
use Illuminate\Console\Command;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';

    protected $description = 'Send scheduled notifications (subscription expiring, appointment reminders)';

    public function handle(): int
    {
        // Send subscription expiring notifications
        $this->sendSubscriptionExpiringNotifications();

        // Send appointment reminder notifications
        $this->sendAppointmentReminders();

        $this->info('Scheduled notifications sent successfully');

        return Command::SUCCESS;
    }

    protected function sendSubscriptionExpiringNotifications(): void
    {
        $expiringSubscriptions = UserSubscription::where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addDays(3)])
            ->get();

        foreach ($expiringSubscriptions as $subscription) {
            dispatch(new SendSubscriptionExpiringNotification($subscription));
        }

        $this->info("Sent {$expiringSubscriptions->count()} subscription expiring notifications");
    }

    protected function sendAppointmentReminders(): void
    {
        // Find appointments in the next 2 hours that haven't sent reminders
        $appointments = Appointment::where('reminder_sent', false)
            ->where('start_time', '>', now())
            ->where('start_time', '<', now()->addHours(2))
            ->whereNotIn('status', ['cancelled'])
            ->with(['user.notificationSetting', 'client'])
            ->get();

        foreach ($appointments as $appointment) {
            dispatch(new SendAppointmentReminderNotification($appointment));
        }

        $this->info("Sent {$appointments->count()} appointment reminder notifications");
    }
}
