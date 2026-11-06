<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentConfirmation;
use App\Models\Seller;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('info', 'Keranjang belanja Anda masih kosong.');
        }

        $user = Auth::user();
        $banks = BankAccount::active()->get();
        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $totalQuantity = array_sum(array_column($cart, 'quantity'));

        // Cek apakah ada produk Pre-Order atau produk brand dengan aturan DP
        $hasPoProduct = false;
        $maxDpPercentage = 30.0; // default jika ada DP
        foreach ($cart as $item) {
            if ($item['product_type'] === 'po') {
                $hasPoProduct = true;
            }
            if (isset($item['dp_percentage']) && $item['dp_percentage'] > $maxDpPercentage) {
                $maxDpPercentage = (float) $item['dp_percentage'];
            }
        }

        // Estimasi nominal DP jika memilih skema DP
        $estimatedDpAmount = round(($maxDpPercentage / 100) * $subtotal);

        // Ambil kupon yang ditandai tampil di checkout
        $availableCoupons = Coupon::active()
            ->where('show_on_checkout', true)
            ->where(function ($q) use ($user) {
                $q->where('target_audience', 'all');
                if ($user) {
                    $q->orWhere('target_audience', 'member_only');
                } else {
                    $q->orWhere('target_audience', 'non_member_only');
                }
            })
            ->get();

        return view('front.checkout', compact(
            'cart', 'user', 'banks', 'subtotal', 'totalQuantity',
            'hasPoProduct', 'maxDpPercentage', 'estimatedDpAmount', 'availableCoupons'
        ));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('catalog')->with('error', 'Keranjang belanja kosong.');
        }

        $validated = $request->validate([
            'recipient_first_name' => 'required|string|max:100',
            'recipient_last_name' => 'nullable|string|max:100',
            'whatsapp' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'courier_name' => 'required|string|max:50',
            'seller_code' => 'nullable|string|max:50',
            'coupon_code' => 'nullable|string|max:50',
            'payment_method_type' => 'required|in:dp,full',
            'customer_notes' => 'nullable|string',
            'different_shipping' => 'nullable|boolean',
            'shipping_first_name' => 'nullable|required_if:different_shipping,1|string|max:100',
            'shipping_address' => 'nullable|required_if:different_shipping,1|string',
            'shipping_city' => 'nullable|required_if:different_shipping,1|string|max:100',
            'shipping_province' => 'nullable|required_if:different_shipping,1|string|max:100',
            'shipping_postal_code' => 'nullable|required_if:different_shipping,1|string|max:10',
        ]);

        $subtotal = array_sum(array_column($cart, 'subtotal'));
        $totalQuantity = array_sum(array_column($cart, 'quantity'));
        $shippingCost = 0; // Flat promo atau dihitung dari ekspedisi

        // Validasi Seller
        $sellerId = null;
        if (! empty($validated['seller_code'])) {
            $seller = Seller::where('seller_code', trim($validated['seller_code']))->where('is_active', true)->first();
            if ($seller) {
                $sellerId = $seller->id;
            }
        }

        // Validasi Kupon
        $couponId = null;
        $discountAmount = 0;
        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper(trim($validated['coupon_code'])))->where('is_active', true)->first();
            if ($coupon) {
                $couponValidation = app(CouponService::class)->validate($coupon, $subtotal, $totalQuantity, Auth::user());
                if ($couponValidation['valid']) {
                    $couponId = $coupon->id;
                    $discountAmount = app(CouponService::class)->calculateDiscount($coupon, $subtotal);
                }
            }
        }

        $grandTotal = max(0, $subtotal - $discountAmount + $shippingCost);

        // Kalkulasi Skema Pembayaran: DP vs Bayar Penuh
        $paymentType = $validated['payment_method_type'];
        $dpAmount = 0;
        $remainingAmount = 0;

        if ($paymentType === 'dp') {
            // Ambil DP percentage tertinggi dari produk di keranjang
            $dpPercent = 30.0;
            foreach ($cart as $item) {
                if (isset($item['dp_percentage']) && $item['dp_percentage'] > $dpPercent) {
                    $dpPercent = (float) $item['dp_percentage'];
                }
            }
            $dpAmount = round(($dpPercent / 100) * $grandTotal);
            $remainingAmount = $grandTotal - $dpAmount;
        }

        // Generate Order Number: ORD[YYMMDD][HHMMSS][RANDOM3]
        $orderNumber = 'ORD'.date('ymdHis').strtoupper(Str::random(3));

        $order = DB::transaction(function () use (
            $orderNumber, $validated, $sellerId, $couponId,
            $subtotal, $discountAmount, $shippingCost, $grandTotal,
            $paymentType, $dpAmount, $remainingAmount, $cart
        ) {
            // Kurangi kuota kupon secara aman dengan locking di dalam transaksi
            if ($couponId) {
                $lockedCoupon = Coupon::where('id', $couponId)->lockForUpdate()->first();
                if ($lockedCoupon && ! is_null($lockedCoupon->quota_total) && $lockedCoupon->quota_total > 0) {
                    $lockedCoupon->decrement('quota_total');
                }
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(), // NULL untuk Guest Checkout
                'seller_id' => $sellerId,
                'coupon_id' => $couponId,
                'recipient_first_name' => $validated['recipient_first_name'],
                'recipient_last_name' => $validated['recipient_last_name'] ?? '',
                'whatsapp' => $validated['whatsapp'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'],
                'courier_name' => $validated['courier_name'],
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'grand_total' => $grandTotal,
                'payment_method_type' => $paymentType,
                'dp_amount' => $dpAmount,
                'remaining_amount' => $remainingAmount,
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'dp_status' => $paymentType === 'dp' ? 'none' : 'fully_paid',
                'customer_notes' => $validated['customer_notes'] ?? null,
                'different_shipping_address' => ! empty($validated['different_shipping']),
                'shipping_first_name' => $validated['shipping_first_name'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'shipping_city' => $validated['shipping_city'] ?? null,
                'shipping_province' => $validated['shipping_province'] ?? null,
                'shipping_postal_code' => $validated['shipping_postal_code'] ?? null,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name_snapshot' => $item['product_name'],
                    'model_name' => $item['model_name'],
                    'color_name' => $item['color_name'],
                    'size_name' => $item['size_name'],
                    'unit_price' => $item['unit_price'],
                    'rule_adjustment' => $item['rule_adjustment'] ?? 0,
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'bonus_item_snapshot' => $item['bonus_item'] ?? null,
                ]);
            }

            return $order;
        });

        // Kosongkan Keranjang Belanja
        session()->forget('cart');

        return redirect()->route('checkout.success', ['orderNumber' => $order->order_number])
            ->with('success', 'Pesanan Anda berhasil dibuat! Silakan lakukan pembayaran transfer.');
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'seller', 'coupon', 'paymentConfirmations.bankAccount'])
            ->firstOrFail();

        $banks = BankAccount::active()->get();

        return view('front.order-confirmation', compact('order', 'banks'));
    }

    public function uploadProof(Request $request, $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:1',
            'payment_stage' => 'required|in:dp,full,remaining',
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
            'notes' => 'nullable|string|max:500',
        ]);

        $filePath = $request->file('proof_file')->store('payment_proofs', 'public');

        PaymentConfirmation::create([
            'order_id' => $order->id,
            'bank_account_id' => $request->bank_account_id,
            'proof_file_path' => $filePath,
            'payment_stage' => $request->payment_stage,
            'amount' => $request->amount,
            'verification_status' => 'pending',
            'notes' => $request->notes,
        ]);

        $order->update([
            'payment_status' => 'waiting_verification',
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi tim admin.');
    }
}
