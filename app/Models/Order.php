<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'seller_id',
        'coupon_id',
        'recipient_first_name',
        'recipient_last_name',
        'whatsapp',
        'address',
        'city',
        'province',
        'postal_code',
        'courier_name',
        'shipping_cost',
        'subtotal',
        'discount',
        'grand_total',
        'payment_method_type',
        'dp_amount',
        'remaining_amount',
        'order_status',
        'payment_status',
        'dp_status',
        'customer_notes',
        'different_shipping_address',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        // Legacy aliases
        'customer_name',
        'customer_phone',
        'shipping_name',
        'shipping_phone',
        'courier',
        'payment_type',
        'payment_method',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'dp_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'different_shipping_address' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentConfirmations(): HasMany
    {
        return $this->hasMany(PaymentConfirmation::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    // ── Static Methods ───────────────────────────────────────────────

    /**
     * Generate a unique order number.
     * Format: PK-YYYYMMDD-XXXXX
     */
    public static function generateOrderNumber(): string
    {
        do {
            $number = 'PK-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getCustomerNameAttribute(): string
    {
        return trim(($this->recipient_first_name ?? '').' '.($this->recipient_last_name ?? ''));
    }

    public function getStatusAttribute(): ?string
    {
        return $this->order_status;
    }

    /**
     * Get a styled status badge (HTML class mapping).
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->order_status ?? $this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-indigo-100 text-indigo-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'completed' => 'bg-green-200 text-green-900',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
