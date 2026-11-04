<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder_name',
        'is_active',
        // Legacy aliases
        'account_holder',
        'logo_path',
        'sort_order',
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
            'is_active' => 'boolean',
        ];
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getAccountHolderAttribute(): ?string
    {
        return $this->account_holder_name;
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
