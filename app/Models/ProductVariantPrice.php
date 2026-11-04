<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantPrice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'product_model_id',
        'product_size_id',
        'price',
        'stock',
        // Legacy aliases
        'price_umum',
        'price_member',
        'price_distributor',
        'sku_variant',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_umum' => 'decimal:2',
            'price_member' => 'decimal:2',
            'price_distributor' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productModel(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class);
    }

    public function productSize(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class);
    }
}
