<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class ClientAccount extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'phone',
        'email',
        'name',
        'password',
        'verification_code',
        'verification_code_expires_at',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    protected function casts(): array
    {
        return [
            'verification_code_expires_at' => 'datetime',
            'is_verified' => 'boolean',
        ];
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'client_account_id');
    }

    public function masters()
    {
        return $this->belongsToMany(User::class, 'appointments', 'client_account_id', 'user_id')
            ->withPivot('created_at')
            ->orderByDesc('pivot_created_at');
    }

    public function generateVerificationCode(): string
    {
        $this->verification_code = sprintf('%06d', mt_rand(0, 999999));
        $this->verification_code_expires_at = now()->addMinutes(10);
        $this->save();
        return $this->verification_code;
    }

    public function verifyCode(string $code): bool
    {
        return $this->verification_code === $code 
            && $this->verification_code_expires_at && $this->verification_code_expires_at->isFuture();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
