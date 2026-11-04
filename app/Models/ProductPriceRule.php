<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceRule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'rule_type',
        'amount',
        'description',
        'condition_variant',
        'is_active',
        // Legacy aliases
        'min_qty',
        'max_qty',
        'discount_type',
        'discount_value',
        'special_price',
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
            'amount' => 'decimal:2',
            'condition_variant' => 'array',
            'min_qty' => 'integer',
            'max_qty' => 'integer',
            'discount_value' => 'decimal:2',
            'special_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
