<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Registration extends Model
{
    protected $fillable = [
        'order_id',
        'full_name',
        'phone',
        'whatsapp_phone',
        'email',
        'address',
        'gender',
        'study_level',
        'training_type',
        'about_me',
        'is_employee',
        'is_engineer',
        'engineer_id_image',
        'status',
        'admin_notes',
        'ip_address',
        'phone_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_engineer'    => 'boolean',
            'phone_verified' => 'boolean',
        ];
    }

    /**
     * Generate unique order ID like ITD-A1B2C3
     */
    public static function generateOrderId(): string
    {
        do {
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $code  = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $orderId = 'ITD-' . $code;
        } while (self::where('order_id', $orderId)->exists());

        return $orderId;
    }

    /* ── Scopes ── */

    public function scopeFilterByStatus(Builder $query, ?string $status): Builder
    {
        return $status && $status !== 'all'
            ? $query->where('status', $status)
            : $query;
    }

    public function scopeFilterByEngineer(Builder $query, ?string $filter): Builder
    {
        if ($filter === 'yes') return $query->where('is_engineer', true);
        if ($filter === 'no') return $query->where('is_engineer', false);
        return $query;
    }

    public function scopeFilterByEmployee(Builder $query, ?string $filter): Builder
    {
        if ($filter === 'yes') return $query->where('is_employee', 'نعم');
        if ($filter === 'no') return $query->where('is_employee', 'لا');
        return $query;
    }

    public function scopeFilterByStudyLevel(Builder $query, ?string $filter): Builder
    {
        return $filter && $filter !== 'all'
            ? $query->where('study_level', $filter)
            : $query;
    }

    public function scopeFilterByTrainingType(Builder $query, ?string $filter): Builder
    {
        return $filter && $filter !== 'all'
            ? $query->where('training_type', $filter)
            : $query;
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) return $query;
        return $query->where(function ($q) use ($search) {
            $q->where('full_name', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%")
              ->orWhere('order_id', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get engineer ID image full URL
     */
    public function getEngineerIdImageUrlAttribute(): ?string
    {
        if (!$this->engineer_id_image) return null;
        return url('storage/' . $this->engineer_id_image);
    }

    protected $appends = ['engineer_id_image_url'];
}
