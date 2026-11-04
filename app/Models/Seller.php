<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seller_code',
        'brand_id',
        'name',
        'phone',
        'is_active',
        // Legacy aliases
        'code',
        'city',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
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
}
