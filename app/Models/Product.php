<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'brand_id',
        'sku',
        'name',
        'slug',
        'product_type',
        'visible_to_umum',
        'visible_to_member',
        'visible_to_distributor',
        'weight_grams',
        'product_note',
        'description',
        'min_price',
        'max_price',
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
            'visible_to_umum' => 'boolean',
            'visible_to_member' => 'boolean',
            'visible_to_distributor' => 'boolean',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }

    public function models(): HasMany
    {
        return $this->hasMany(ProductModel::class);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function variantPrices(): HasMany
    {
        return $this->hasMany(ProductVariantPrice::class);
    }

    public function bonuses(): HasMany
    {
        return $this->hasMany(ProductBonus::class);
    }

    public function priceRules(): HasMany
    {
        return $this->hasMany(ProductPriceRule::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeReadyStock(Builder $query): Builder
    {
        return $query->where('product_type', 'ready_stock');
    }

    public function scopePreOrder(Builder $query): Builder
    {
        return $query->where('product_type', 'pre_order');
    }

    /**
     * Scope products visible to a specific customer type.
     */
    public function scopeVisibleTo(Builder $query, ?string $customerType): Builder
    {
        return match ($customerType) {
            'member' => $query->where('visible_to_member', true),
            'distributor' => $query->where('visible_to_distributor', true),
            default => $query->where('visible_to_umum', true),
        };
    }

    // ── Accessors ────────────────────────────────────────────────────

    /**
     * Get the primary (first) image for this product.
     */
    public function getPrimaryImageAttribute(): ?ProductImage
    {
        return $this->images->sortBy('sort_order')->first();
    }

    /**
     * Get the primary image URL for this product.
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primary = $this->primary_image;

        return $primary ? $primary->url : null;
    }

    // ── Helper Methods ───────────────────────────────────────────────

    /**
     * Recalculate and update the min/max price range from variant prices.
     */
    public function updatePriceRange(): void
    {
        $prices = $this->variantPrices()->pluck('price_umum');

        if ($prices->isNotEmpty()) {
            $this->update([
                'min_price' => $prices->min(),
                'max_price' => $prices->max(),
            ]);
        }
    }
}
