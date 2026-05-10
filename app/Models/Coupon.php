<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Coupon extends Model
{
    protected $fillable = [
        'organization_id',
        'created_by',
        'approved_by',
        'recipient_name',
        'qr_code',
        'quantity',
        'weight_kg',
        'status',
        'approved_at',
        'claimed_at',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('organization_id', Auth::user()->organization_id);
            }
        });

        static::creating(function ($coupon) {
            if (Auth::check() && !$coupon->organization_id) {
                $coupon->organization_id = Auth::user()->organization_id;
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'claimed_at' => 'datetime',
        ];
    }
}
