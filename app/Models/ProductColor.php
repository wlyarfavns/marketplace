<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductColor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'color_name',
        'hex_code',
        'color_image_path',
        'sort_order',
    ];

    /**
     * The accessors to append to the model's array and JSON form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get URL for color-specific image if present.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->color_image_path)) {
            return null;
        }

        if (str_starts_with($this->color_image_path, 'http://') || str_starts_with($this->color_image_path, 'https://')) {
            return $this->color_image_path;
        }

        $cleanPath = ltrim($this->color_image_path, '/');

        if (str_starts_with($cleanPath, 'images/')) {
            return asset($cleanPath);
        }

        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        if (file_exists(public_path($cleanPath))) {
            return asset($cleanPath);
        }

        return asset('storage/'.$cleanPath);
    }

    // ── Relationships ────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
