<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\Request;

class AdminSellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::with('brand')->withCount('orders')->latest()->paginate(15);
        $brands = Brand::active()->get();

        return view('admin.sellers.index', compact('sellers', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_code' => 'required|string|max:50|unique:sellers,seller_code',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Seller::create(array_merge($validated, ['is_active' => true]));

        return redirect()->back()->with('success', 'Mitra Seller baru berhasil didaftarkan.');
    }

    public function update(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);
        $validated = $request->validate([
            'seller_code' => 'required|string|max:50|unique:sellers,seller_code,'.$seller->id,
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        $seller->update($validated);

        return redirect()->back()->with('success', 'Data Mitra Seller berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->delete();

        return redirect()->back()->with('success', 'Data Seller berhasil dihapus.');
    }

    // Modul 7: Laporan Penjualan Mitra
    public function salesReport(Request $request)
    {
        $selectedBrand = $request->input('brand_id');
        $selectedSeller = $request->input('seller_id');

        $sellersQuery = Seller::with('brand')->where('is_active', true);
        if ($selectedBrand) {
            $sellersQuery->where('brand_id', $selectedBrand);
        }
        if ($selectedSeller) {
            $sellersQuery->where('id', $selectedSeller);
        }
        $sellers = $sellersQuery->get();

        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfLastMonth = now()->subMonth()->startOfMonth()->toDateString();
        $endOfLastMonth = now()->subMonth()->endOfMonth()->toDateString();

        $reportData = [];
        $totalClosingToday = 0;
        $totalSalesMonth = 0;
        $totalSalesLastMonth = 0;

        foreach ($sellers as $s) {
            // Closing Hari Ini
            $todayClosing = Order::where('seller_id', $s->id)
                ->whereDate('created_at', $today)
                ->count();

            // Total Penjualan Bulan Ini
            $monthSales = Order::where('seller_id', $s->id)
                ->where('created_at', '>=', $startOfMonth)
                ->where('payment_status', 'verified')
                ->sum('grand_total');

            // Total Penjualan Bulan Lalu
            $lastMonthSales = Order::where('seller_id', $s->id)
                ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
                ->where('payment_status', 'verified')
                ->sum('grand_total');

            $totalOrdersCount = Order::where('seller_id', $s->id)->count();

            $totalClosingToday += $todayClosing;
            $totalSalesMonth += $monthSales;
            $totalSalesLastMonth += $lastMonthSales;

            $reportData[] = [
                'seller' => $s,
                'closing_today' => $todayClosing,
                'sales_month' => $monthSales,
                'sales_last_month' => $lastMonthSales,
                'total_orders' => $totalOrdersCount,
            ];
        }

        $brands = Brand::active()->get();
        $allSellers = Seller::active()->get();

        return view('admin.sellers.report', compact(
            'reportData', 'brands', 'allSellers',
            'totalClosingToday', 'totalSalesMonth', 'totalSalesLastMonth'
        ));
    }

    // Export Laporan Penjualan Mitra ke Excel / CSV
    public function exportSalesReport()
    {
        $fileName = 'Laporan-Performa-Mitra-PasarKita-'.date('Y-m-d').'.csv';
        $sellers = Seller::with('brand')->where('is_active', true)->get();

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID Seller', 'Nama Mitra', 'Brand Terkait', 'WhatsApp', 'Closing Hari Ini', 'Omzet Bulan Ini', 'Omzet Bulan Lalu'];

        $callback = function () use ($sellers, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            $today = now()->toDateString();
            $startOfMonth = now()->startOfMonth()->toDateString();
            $startOfLastMonth = now()->subMonth()->startOfMonth()->toDateString();
            $endOfLastMonth = now()->subMonth()->endOfMonth()->toDateString();

            foreach ($sellers as $s) {
                $todayClosing = Order::where('seller_id', $s->id)->whereDate('created_at', $today)->count();
                $monthSales = Order::where('seller_id', $s->id)->where('created_at', '>=', $startOfMonth)->where('payment_status', 'verified')->sum('grand_total');
                $lastMonthSales = Order::where('seller_id', $s->id)->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->where('payment_status', 'verified')->sum('grand_total');

                fputcsv($file, [
                    $s->seller_code,
                    $s->name,
                    $s->brand ? $s->brand->name : '-',
                    $s->phone,
                    $todayClosing,
                    $monthSales,
                    $lastMonthSales,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
