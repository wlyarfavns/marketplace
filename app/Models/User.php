<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'customer_type',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'default_seller_code',
        'favorite_courier',
        'status',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'customer_type' => 'string',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function paymentVerifications(): HasMany
    {
        return $this->hasMany(PaymentConfirmation::class, 'verified_by');
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    public function scopeCustomer(Builder $query): Builder
    {
        return $query->where('role', 'customer');
    }

    public function scopeMember(Builder $query): Builder
    {
        return $query->where('customer_type', 'member');
    }

    public function scopeDistributor(Builder $query): Builder
    {
        return $query->where('customer_type', 'distributor');
    }

    // ── Helper Methods ───────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isMember(): bool
    {
        return $this->customer_type === 'member';
    }

    public function isDistributor(): bool
    {
        return $this->customer_type === 'distributor';
    }

    public function isUmum(): bool
    {
        return $this->customer_type === 'umum' || $this->customer_type === null;
    }
}
