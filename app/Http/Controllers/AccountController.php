<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentConfirmation;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Dashboard ringkasan akun pelanggan.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Statistik Belanja Konsolidasi
        $stats = Order::where('user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total_orders,
                COUNT(CASE WHEN order_status IN ('pending', 'processing', 'shipped') THEN 1 END) as active_orders,
                COUNT(CASE WHEN order_status = 'completed' THEN 1 END) as completed_orders,
                COALESCE(SUM(CASE WHEN payment_status = 'verified' THEN grand_total ELSE 0 END), 0) as total_spent
            ")
            ->first();

        $totalOrders = (int) ($stats->total_orders ?? 0);
        $activeOrders = (int) ($stats->active_orders ?? 0);
        $completedOrders = (int) ($stats->completed_orders ?? 0);
        $totalSpent = (float) ($stats->total_spent ?? 0);

        $totalTransactions = PaymentConfirmation::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        $recentOrders = Order::where('user_id', $user->id)
            ->with(['items.product.images'])
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = PaymentConfirmation::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['order', 'bankAccount'])
        ->latest()
        ->take(5)
        ->get();

        return view('account.dashboard', compact(
            'user', 'totalOrders', 'activeOrders', 'completedOrders', 'totalSpent', 'totalTransactions', 'recentOrders', 'recentTransactions'
        ));
    }

    /**
     * Riwayat Pemesanan (Order History) pelanggan.
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = Order::where('user_id', $user->id);

        // Pencarian nomor pesanan
        if ($search = $request->input('search')) {
            $query->where('order_number', 'like', '%' . trim($search) . '%');
        }

        // Filter status pesanan
        if ($status = $request->input('status')) {
            $query->where('order_status', $status);
        }

        // Filter status pembayaran
        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        // Filter rentang tanggal
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Pengurutan
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $orders = $query->with(['items.product.images', 'paymentConfirmations.bankAccount', 'seller'])
            ->paginate(10)
            ->withQueryString();

        return view('account.orders', compact('user', 'orders'));
    }

    /**
     * Rincian detail pesanan pelanggan.
     */
    public function orderDetail($orderNumber)
    {
        $user = Auth::user();
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with(['items.product.images', 'seller', 'coupon', 'paymentConfirmations.bankAccount', 'invoice'])
            ->firstOrFail();

        return view('account.order-detail', compact('order'));
    }

    /**
     * Riwayat Transaksi Pembayaran (Transaction History) pelanggan.
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        $query = PaymentConfirmation::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['order.items.product.images', 'bankAccount', 'verifiedByUser']);

        // Pencarian nomor transaksi (TRX-XXXXXX) atau nomor pesanan (ORD...)
        if ($search = $request->input('search')) {
            $cleanSearch = trim($search);
            $query->where(function ($q) use ($cleanSearch) {
                if (preg_match('/^TRX-0*(\d+)$/i', $cleanSearch, $matches)) {
                    $q->where('id', (int) $matches[1]);
                } elseif (is_numeric($cleanSearch)) {
                    $q->where('id', (int) $cleanSearch);
                }
                $q->orWhereHas('order', function ($oq) use ($cleanSearch) {
                    $oq->where('order_number', 'like', "%{$cleanSearch}%");
                });
            });
        }

        // Filter status verifikasi transaksi
        if ($status = $request->input('status')) {
            $query->where('verification_status', $status);
        }

        // Filter tahap pembayaran (dp, full, remaining)
        if ($stage = $request->input('stage')) {
            $query->where('payment_stage', $stage);
        }

        // Filter rentang tanggal transaksi
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Pengurutan transaksi
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $transactions = $query->paginate(10)->withQueryString();

        return view('account.transactions', compact('user', 'transactions'));
    }

    /**
     * Rincian detail transaksi pembayaran tertentu.
     */
    public function transactionDetail($id)
    {
        $user = Auth::user();
        $transaction = PaymentConfirmation::where('id', $id)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['order.items.product.images', 'order.seller', 'bankAccount', 'verifiedByUser'])
            ->firstOrFail();

        return view('account.transaction-detail', compact('transaction'));
    }

    /**
     * Halaman profil pelanggan.
     */
    public function profile()
    {
        $user = Auth::user();
        $sellers = Seller::active()->get();

        return view('account.profile', compact('user', 'sellers'));
    }

    /**
     * Perbarui data profil pelanggan.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'default_seller_code' => 'nullable|string|max:50',
            'favorite_courier' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profil dan preferensi akun Anda berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi pelanggan.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
