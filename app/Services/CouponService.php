<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;

class CouponService
{
    /**
     * Validate a coupon against given cart subtotal, quantity, and user.
     *
     * @return array{valid: bool, message: string, status: int}
     */
    public function validate(Coupon $coupon, float $subtotal, int $totalQuantity = 1, ?User $user = null): array
    {
        if (! $coupon->is_active) {
            return [
                'valid' => false,
                'message' => 'Kode voucher kupon tidak ditemukan atau kadaluarsa.',
                'status' => 404,
            ];
        }

        // Cek Tanggal Berlaku
        if ($coupon->starts_at && now()->lt($coupon->starts_at)) {
            return [
                'valid' => false,
                'message' => 'Voucher belum mulai berlaku.',
                'status' => 400,
            ];
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return [
                'valid' => false,
                'message' => 'Masa berlaku voucher telah habis.',
                'status' => 400,
            ];
        }

        // Cek Kuota Total
        if (! is_null($coupon->quota_total) && $coupon->quota_total <= 0) {
            return [
                'valid' => false,
                'message' => 'Kuota pemakaian voucher telah habis.',
                'status' => 400,
            ];
        }

        // Cek Target Audiens
        if ($coupon->target_audience === 'member_only' && ! $user) {
            return [
                'valid' => false,
                'message' => 'Voucher ini khusus untuk akun Pelanggan terdaftar.',
                'status' => 400,
            ];
        }

        if ($coupon->target_audience === 'non_member_only' && $user) {
            return [
                'valid' => false,
                'message' => 'Voucher ini khusus untuk Pengunjung baru.',
                'status' => 400,
            ];
        }

        // Cek Minimal Belanja
        if ($coupon->min_purchase > 0 && $subtotal < $coupon->min_purchase) {
            return [
                'valid' => false,
                'message' => 'Minimal belanja untuk voucher ini adalah Rp '.number_format($coupon->min_purchase, 0, ',', '.'),
                'status' => 400,
            ];
        }

        // Cek Minimal Kuantitas
        if ($coupon->min_quantity > 0 && $totalQuantity < $coupon->min_quantity) {
            return [
                'valid' => false,
                'message' => "Minimal pembelian {$coupon->min_quantity} item untuk kupon ini.",
                'status' => 400,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Kupon berhasil diterapkan!',
            'status' => 200,
        ];
    }

    /**
     * Calculate discount amount for a validated coupon and subtotal.
     */
    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = 0.0;

        if ($coupon->discount_type === 'percentage') {
            $discount = ($coupon->discount_value / 100) * $subtotal;
            if ($coupon->max_discount && $discount > $coupon->max_discount) {
                $discount = (float) $coupon->max_discount;
            }
        } else {
            $discount = min((float) $coupon->discount_value, $subtotal);
        }

        return max(0.0, round($discount, 2));
    }

    /**
     * Check and calculate coupon in a single call (used by AJAX checkCoupon).
     */
    public function check(string $code, float $subtotal, int $totalQuantity = 1, ?User $user = null): array
    {
        $normalizedCode = strtoupper(trim($code));

        $coupon = Coupon::where('code', $normalizedCode)->where('is_active', true)->first();

        if (! $coupon) {
            return [
                'valid' => false,
                'message' => 'Kode voucher kupon tidak ditemukan atau kadaluarsa.',
                'status' => 404,
            ];
        }

        $validation = $this->validate($coupon, $subtotal, $totalQuantity, $user);

        if (! $validation['valid']) {
            return $validation;
        }

        $discount = $this->calculateDiscount($coupon, $subtotal);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'discount_amount' => $discount,
            'discount_formatted' => 'Rp '.number_format($discount, 0, ',', '.'),
            'message' => 'Kupon berhasil diterapkan!',
            'status' => 200,
        ];
    }
}

