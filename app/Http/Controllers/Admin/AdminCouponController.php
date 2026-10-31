<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('orders')->latest()->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'max_discount' => 'nullable|numeric|min:0',
            'target_audience' => 'required|in:all,member_only,non_member_only',
            'min_purchase' => 'nullable|numeric|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'quota_total' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'show_on_checkout' => 'nullable|boolean',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'max_discount' => $validated['max_discount'] ?? null,
            'target_audience' => $validated['target_audience'],
            'min_purchase' => $validated['min_purchase'] ?? 0,
            'min_quantity' => $validated['min_quantity'] ?? 0,
            'quota_total' => $validated['quota_total'] ?? null,
            'starts_at' => $validated['starts_at'] ?? now(),
            'expires_at' => $validated['expires_at'] ?? null,
            'show_on_checkout' => ! empty($validated['show_on_checkout']),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Kupon pemasaran baru berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,'.$coupon->id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:1',
            'max_discount' => 'nullable|numeric|min:0',
            'target_audience' => 'required|in:all,member_only,non_member_only',
            'min_purchase' => 'nullable|numeric|min:0',
            'min_quantity' => 'nullable|integer|min:0',
            'quota_total' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'show_on_checkout' => 'nullable|boolean',
            'is_active' => 'required|boolean',
        ]);

        $coupon->update([
            'code' => strtoupper(trim($validated['code'])),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'max_discount' => $validated['max_discount'] ?? null,
            'target_audience' => $validated['target_audience'],
            'min_purchase' => $validated['min_purchase'] ?? 0,
            'min_quantity' => $validated['min_quantity'] ?? 0,
            'quota_total' => $validated['quota_total'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'show_on_checkout' => ! empty($validated['show_on_checkout']),
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->back()->with('success', 'Data Kupon berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->back()->with('success', 'Kupon berhasil dihapus.');
    }
}
