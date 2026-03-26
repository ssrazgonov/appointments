<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'duration_days',
        'max_clients',
        'max_appointments_per_day',
        'is_active',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'max_clients' => 'integer',
            'max_appointments_per_day' => 'integer',
            'is_active' => 'boolean',
            'features' => 'array',
        ];
    }

    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
