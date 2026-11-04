<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBonus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'free_item_name',
        'free_item_description',
        'min_quantity',
        'condition_variant',
        'is_active',
        // Legacy aliases
        'title',
        'description',
        'condition_type',
        'condition_min_qty',
        'bonus_item',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition_variant' => 'array',
            'min_quantity' => 'integer',
            'condition_min_qty' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
