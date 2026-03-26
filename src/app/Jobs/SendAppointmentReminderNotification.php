<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Appointment $appointment
    ) {}

    public function handle(): void
    {
        $user = $this->appointment->user;
        $client = $this->appointment->client;
        $settings = $user->notificationSetting;

        if (!$settings) {
            return;
        }

        $minutesBefore = $settings->reminder_minutes_before ?? 60;
        $reminderTime = $this->appointment->start_time->copy()->subMinutes($minutesBefore);

        if ($reminderTime->isFuture()) {
            return;
        }

        if ($this->appointment->reminder_sent) {
            return;
        }

        Log::info('Sending appointment reminder notification', [
            'appointment_id' => $this->appointment->id,
            'user_id' => $user->id,
            'client_name' => $client->full_name ?? 'Unknown',
            'start_time' => $this->appointment->start_time,
        ]);

        // Mark reminder as sent
        $this->appointment->update(['reminder_sent' => true]);

        // Here you would send an actual email/SMS
        // if ($settings->email_appointment_reminder) {
        //     $user->notify(new AppointmentReminderNotification($this->appointment));
        // }
    }
}
