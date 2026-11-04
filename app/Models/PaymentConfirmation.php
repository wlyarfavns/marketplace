<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentConfirmation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'bank_account_id',
        'proof_file_path',
        'payment_stage',
        'amount',
        'verification_status',
        'verified_by',
        'verified_at',
        'notes',
        // Legacy aliases
        'payment_type',
        'sender_name',
        'sender_bank',
        'transfer_date',
        'proof_image_path',
        'status',
        'rejection_reason',
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
            'transfer_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getProofImagePathAttribute(): ?string
    {
        return $this->proof_file_path;
    }

    public function getStatusAttribute(): ?string
    {
        return $this->verification_status;
    }

    // ── Relationships ────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
