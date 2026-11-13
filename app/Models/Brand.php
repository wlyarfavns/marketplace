<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'dp_percentage',
        'description',
        'show_on_homepage',
        'homepage_order',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dp_percentage' => 'decimal:2',
            'show_on_homepage' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }

        if (str_starts_with($this->logo_path, 'http://') || str_starts_with($this->logo_path, 'https://')) {
            return $this->logo_path;
        }

        $cleanPath = ltrim($this->logo_path, '/');

        if (str_starts_with($cleanPath, 'images/')) {
            return asset($cleanPath);
        }

        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        return asset('storage/' . $cleanPath);
    }

    // ── Relationships ────────────────────────────────────────────────

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeHomepage(Builder $query): Builder
    {
        return $query->where('show_on_homepage', true)
            ->where('is_active', true)
            ->orderBy('homepage_order');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('homepage_order');
    }
}
