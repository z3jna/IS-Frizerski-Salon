<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public const ROLE_ADMIN = 'administrator';
    public const ROLE_ZAPOSLENI = 'zaposleni';
    public const ROLE_KLIJENT = 'klijent';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }

    public function klijent(): HasOne
    {
        return $this->hasOne(Klijent::class);
    }

    public function zaposleni(): HasOne
    {
        return $this->hasOne(Zaposleni::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isZaposleni(): bool
    {
        return $this->role === self::ROLE_ZAPOSLENI;
    }

    public function isKlijent(): bool
    {
        return $this->role === self::ROLE_KLIJENT;
    }
}
