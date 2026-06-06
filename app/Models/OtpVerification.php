<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = [
        'phone',
        'code',
        'attempts',
        'verified',
        'expires_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'verified'   => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(string $code): bool
    {
        return !$this->isExpired() && !$this->verified && $this->code === $code;
    }
}
