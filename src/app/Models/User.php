<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'business_name',
        'business_type',
        'bio',
        'timezone',
        'avatar_url',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function subscription()
    {
        return $this->hasOne(UserSubscription::class)->latest();
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function notificationSetting()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    public function hasActiveSubscription(): bool
    {
        $subscription = $this->subscription;
        return $subscription && $subscription->isActive();
    }

    public function masterProfile()
    {
        return $this->hasOne(MasterProfile::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function workingHours()
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function activeServices()
    {
        return $this->hasMany(Service::class)->where('is_active', true)->orderBy('sort_order');
    }
}
