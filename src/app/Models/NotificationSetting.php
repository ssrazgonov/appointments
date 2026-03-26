<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_appointment_reminder',
        'sms_appointment_reminder',
        'reminder_minutes_before',
        'email_new_client',
        'email_payment_received',
        'email_subscription_expiring',
        'subscription_expire_days_before',
    ];

    protected function casts(): array
    {
        return [
            'email_appointment_reminder' => 'boolean',
            'sms_appointment_reminder' => 'boolean',
            'reminder_minutes_before' => 'integer',
            'email_new_client' => 'boolean',
            'email_payment_received' => 'boolean',
            'email_subscription_expiring' => 'boolean',
            'subscription_expire_days_before' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
