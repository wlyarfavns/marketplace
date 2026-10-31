<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Utama
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'verified')->sum('grand_total');
        $totalCustomers = User::where('role', 'customer')->count();
        $activeProducts = Product::where('status', 'active')->count();

        // 2. Status Operasional
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $processingOrders = Order::where('order_status', 'processing')->count();
        $completedOrders = Order::where('order_status', 'completed')->count();

        // 3. Statistik Khusus Down Payment (DP)
        $totalDpOrders = Order::where('payment_method_type', 'dp')->count();
        $dpPaidOrders = Order::where('payment_method_type', 'dp')->whereIn('dp_status', ['dp_paid', 'fully_paid'])->count();
        $dpPendingOrders = Order::where('payment_method_type', 'dp')->where('dp_status', 'none')->count();
        $fullyPaidOrders = Order::where('dp_status', 'fully_paid')->count();

        $revenueDp = Order::where('payment_method_type', 'dp')
            ->whereIn('dp_status', ['dp_paid', 'fully_paid'])
            ->sum('dp_amount');

        $remainingReceivable = Order::where('payment_method_type', 'dp')
            ->where('dp_status', 'dp_paid')
            ->sum('remaining_amount');

        // 4. Daftar 5 Pesanan Terbaru
        $recentOrders = Order::with(['user', 'seller'])
            ->latest()
            ->take(5)
            ->get();

        // 5. Produk Terpopuler (Berdasarkan jumlah terjual)
        $popularProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();

        // 6. Ringkasan Kupon Aktif
        $activeCoupons = Coupon::active()->latest()->take(4)->get();

        return view('admin.dashboard', compact(
            'totalOrders', 'totalRevenue', 'totalCustomers', 'activeProducts',
            'pendingOrders', 'processingOrders', 'completedOrders',
            'totalDpOrders', 'dpPaidOrders', 'dpPendingOrders', 'fullyPaidOrders',
            'revenueDp', 'remainingReceivable',
            'recentOrders', 'popularProducts', 'activeCoupons'
        ));
    }
}
