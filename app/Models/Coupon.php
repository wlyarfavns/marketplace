<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_discount',
        'target_audience',
        'min_purchase',
        'min_quantity',
        'quota_total',
        'quota_per_user',
        'restriction_type',
        'show_on_checkout',
        'starts_at',
        'expires_at',
        'is_active',
        // Legacy aliases
        'name',
        'description',
        'usage_limit',
        'used_count',
        'per_user_limit',
        'applies_to',
        'customer_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'min_quantity' => 'integer',
            'quota_total' => 'integer',
            'quota_per_user' => 'integer',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'per_user_limit' => 'integer',
            'show_on_checkout' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_categories');
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'coupon_brands');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_products');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            })
            ->where(function (Builder $q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    // ── Helper Methods ───────────────────────────────────────────────

    /**
     * Check if this coupon is currently valid (active, within date range, usage not exceeded).
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if this coupon is valid for a specific user.
     */
    public function isValidForUser(User $user): bool
    {
        if (! $this->isValid()) {
            return false;
        }

        // Check customer type restriction
        if ($this->customer_type && $this->customer_type !== $user->customer_type) {
            return false;
        }

        // Check per-user usage limit
        if ($this->per_user_limit !== null) {
            $userUsageCount = Order::where('user_id', $user->id)
                ->where('coupon_id', $this->id)
                ->count();

            if ($userUsageCount >= $this->per_user_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->min_purchase && $subtotal < (float) $this->min_purchase) {
            return 0;
        }

        $discount = match ($this->discount_type) {
            'percentage' => $subtotal * ((float) $this->discount_value / 100),
            'fixed' => (float) $this->discount_value,
            default => 0,
        };

        // Apply max discount cap for percentage discounts
        if ($this->max_discount && $discount > (float) $this->max_discount) {
            $discount = (float) $this->max_discount;
        }

        // Discount cannot exceed subtotal
        return min($discount, $subtotal);
    }
}
