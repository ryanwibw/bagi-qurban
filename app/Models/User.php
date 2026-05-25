<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'owner_id');
    }

    public function roleInOrganization($organizationId): ?string
    {
        return $this->organizations()
            ->where('organization_id', $organizationId)
            ->first()
            ?->pivot
            ?->role;
    }

    public function isAdmin($organizationId = null): bool
    {
        $organizationId = $organizationId ?: session('active_organization_id');
        return $this->roleInOrganization($organizationId) === 'admin';
    }

    public function isPanitia($organizationId = null): bool
    {
        $organizationId = $organizationId ?: session('active_organization_id');
        return $this->roleInOrganization($organizationId) === 'panitia';
    }

    public function createdCoupons(): HasMany
    {
        return $this->hasMany(Coupon::class, 'created_by');
    }

    public function approvedCoupons(): HasMany
    {
        return $this->hasMany(Coupon::class, 'approved_by');
    }
}
