<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'birth_date',
        'notes',
        'custom_fields',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'custom_fields' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getTotalAppointmentsAttribute(): int
    {
        return $this->appointments()->count();
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->appointments()->sum('price');
    }
}
