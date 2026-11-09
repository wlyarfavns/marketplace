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

    public function getTransactionNumberAttribute(): string
    {
        return 'TRX-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->verification_status ?? $this->status) {
            'verified' => 'bg-neutral-900 text-white border-neutral-900',
            'rejected' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-amber-50 text-amber-800 border-amber-200',
        };
    }

    public function getStageLabelAttribute(): string
    {
        return match ($this->payment_stage ?? $this->payment_type) {
            'dp' => 'Uang Muka (DP)',
            'remaining' => 'Pelunasan Sisa',
            default => 'Bayar Penuh (100%)',
        };
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
