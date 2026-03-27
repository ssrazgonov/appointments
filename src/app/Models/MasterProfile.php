<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'display_name',
        'bio',
        'avatar_url',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
        'appointment_duration',
        'booking_advance_days',
        'is_active',
        'social_links',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'appointment_duration' => 'integer',
            'booking_advance_days' => 'integer',
            'is_active' => 'boolean',
            'social_links' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasManyThrough(Service::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function workingHours()
    {
        return $this->hasManyThrough(WorkingHour::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function getPublicBookingUrlAttribute(): string
    {
        return url("/book/{$this->slug}");
    }
}
