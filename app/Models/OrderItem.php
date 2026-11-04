<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name_snapshot',
        'model_name',
        'color_name',
        'size_name',
        'unit_price',
        'rule_adjustment',
        'quantity',
        'subtotal',
        'bonus_item_snapshot',
        // Legacy aliases
        'product_name',
        'variant_label',
        'color',
        'bonus_info',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'rule_adjustment' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getProductNameAttribute(): ?string
    {
        return $this->product_name_snapshot;
    }

    // ── Relationships ────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
