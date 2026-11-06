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
        // 1. Konsolidasi Metrik Pesanan dalam 1 Kueri Agregat (Menggantikan 11 kueri terpisah)
        $orderStats = Order::query()->selectRaw("
            COUNT(*) as total_orders,
            COALESCE(SUM(CASE WHEN payment_status = 'verified' THEN grand_total ELSE 0 END), 0) as total_revenue,
            COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_orders,
            COUNT(CASE WHEN order_status = 'processing' THEN 1 END) as processing_orders,
            COUNT(CASE WHEN order_status = 'completed' THEN 1 END) as completed_orders,
            COUNT(CASE WHEN payment_method_type = 'dp' THEN 1 END) as total_dp_orders,
            COUNT(CASE WHEN payment_method_type = 'dp' AND dp_status IN ('dp_paid', 'fully_paid') THEN 1 END) as dp_paid_orders,
            COUNT(CASE WHEN payment_method_type = 'dp' AND dp_status = 'none' THEN 1 END) as dp_pending_orders,
            COUNT(CASE WHEN dp_status = 'fully_paid' THEN 1 END) as fully_paid_orders,
            COALESCE(SUM(CASE WHEN payment_method_type = 'dp' AND dp_status IN ('dp_paid', 'fully_paid') THEN dp_amount ELSE 0 END), 0) as revenue_dp,
            COALESCE(SUM(CASE WHEN payment_method_type = 'dp' AND dp_status = 'dp_paid' THEN remaining_amount ELSE 0 END), 0) as remaining_receivable
        ")->first();

        $totalOrders = (int) ($orderStats->total_orders ?? 0);
        $totalRevenue = (float) ($orderStats->total_revenue ?? 0);
        $totalCustomers = User::where('role', 'customer')->count();
        $activeProducts = Product::where('status', 'active')->count();

        // 2. Status Operasional
        $pendingOrders = (int) ($orderStats->pending_orders ?? 0);
        $processingOrders = (int) ($orderStats->processing_orders ?? 0);
        $completedOrders = (int) ($orderStats->completed_orders ?? 0);

        // 3. Statistik Khusus Down Payment (DP)
        $totalDpOrders = (int) ($orderStats->total_dp_orders ?? 0);
        $dpPaidOrders = (int) ($orderStats->dp_paid_orders ?? 0);
        $dpPendingOrders = (int) ($orderStats->dp_pending_orders ?? 0);
        $fullyPaidOrders = (int) ($orderStats->fully_paid_orders ?? 0);
        $revenueDp = (float) ($orderStats->revenue_dp ?? 0);
        $remainingReceivable = (float) ($orderStats->remaining_receivable ?? 0);

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
