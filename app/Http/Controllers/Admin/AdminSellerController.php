<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminSellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::with('brand')->withCount('orders')->latest()->paginate(8);
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
        $allMatchingSellers = (clone $sellersQuery)->get();
        $sellerIds = $allMatchingSellers->pluck('id');

        $startOfToday = now()->startOfDay()->toDateTimeString();
        $startOfMonth = now()->startOfMonth()->toDateTimeString();
        $startOfLastMonth = now()->subMonth()->startOfMonth()->toDateTimeString();
        $endOfLastMonth = now()->subMonth()->endOfMonth()->toDateTimeString();

        $totalClosingToday = 0;
        $totalSalesMonth = 0;
        $totalSalesLastMonth = 0;

        if ($sellerIds->isNotEmpty()) {
            $summary = Order::whereIn('seller_id', $sellerIds)
                ->selectRaw("
                    COUNT(CASE WHEN created_at >= ? THEN 1 END) as total_closing_today,
                    COALESCE(SUM(CASE WHEN created_at >= ? AND payment_status = 'verified' THEN grand_total ELSE 0 END), 0) as total_sales_month,
                    COALESCE(SUM(CASE WHEN created_at >= ? AND created_at <= ? AND payment_status = 'verified' THEN grand_total ELSE 0 END), 0) as total_sales_last_month
                ", [$startOfToday, $startOfMonth, $startOfLastMonth, $endOfLastMonth])
                ->first();

            $totalClosingToday = (int) ($summary->total_closing_today ?? 0);
            $totalSalesMonth = (float) ($summary->total_sales_month ?? 0);
            $totalSalesLastMonth = (float) ($summary->total_sales_last_month ?? 0);
        }

        $paginatedSellers = $sellersQuery->paginate(8)->withQueryString();
        $paginatedSellerIds = $paginatedSellers->pluck('id');

        $sellerMetrics = $paginatedSellerIds->isEmpty() ? collect() : Order::whereIn('seller_id', $paginatedSellerIds)
            ->selectRaw("
                seller_id,
                COUNT(*) as total_orders,
                COUNT(CASE WHEN created_at >= ? THEN 1 END) as today_closing,
                COALESCE(SUM(CASE WHEN created_at >= ? AND payment_status = 'verified' THEN grand_total ELSE 0 END), 0) as month_sales,
                COALESCE(SUM(CASE WHEN created_at >= ? AND created_at <= ? AND payment_status = 'verified' THEN grand_total ELSE 0 END), 0) as last_month_sales
            ", [$startOfToday, $startOfMonth, $startOfLastMonth, $endOfLastMonth])
            ->groupBy('seller_id')
            ->get()
            ->keyBy('seller_id');

        $reportData = [];

        foreach ($paginatedSellers as $s) {
            $metric = $sellerMetrics->get($s->id);
            $reportData[] = [
                'seller' => $s,
                'closing_today' => (int) ($metric->today_closing ?? 0),
                'sales_month' => (float) ($metric->month_sales ?? 0),
                'sales_last_month' => (float) ($metric->last_month_sales ?? 0),
                'total_orders' => (int) ($metric->total_orders ?? 0),
            ];
        }

        $brands = Brand::active()->get();
        $allSellers = Seller::active()->get();

        return view('admin.sellers.report', compact(
            'reportData', 'paginatedSellers', 'brands', 'allSellers',
            'totalClosingToday', 'totalSalesMonth', 'totalSalesLastMonth'
        ));
    }

    // Export Laporan Penjualan Mitra ke Microsoft Excel (.XLSX)
    public function exportSalesReport(Request $request)
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

        $startOfToday = now()->startOfDay()->toDateTimeString();
        $startOfMonth = now()->startOfMonth()->toDateTimeString();
        $startOfLastMonth = now()->subMonth()->startOfMonth()->toDateTimeString();
        $endOfLastMonth = now()->subMonth()->endOfMonth()->toDateTimeString();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Performa Mitra');

        // 1. Header Judul Laporan
        $sheet->setCellValue('A1', 'FOOTWORK — TOKO SEPATU BRANDED & SNEAKERS ORIGINAL');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('0F172A'));

        $sheet->setCellValue('A2', 'LAPORAN PERFORMA PENJUALAN & CLOSING MITRA SELLER');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new Color('334155'));

        $sheet->setCellValue('A3', 'Dicetak: '.now()->format('d/m/Y H:i').' WIB | Total Mitra: '.$sellers->count().' Orang');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9)->setColor(new Color('64748B'));

        // 2. Baris Header Tabel
        $headers = [
            'No.',
            'ID Seller (Kode)',
            'Nama Mitra Seller',
            'Brand Terkait',
            'No. WhatsApp',
            'Closing Hari Ini',
            'Total Order (All-Time)',
            'Omzet Bulan Ini',
            'Omzet Bulan Lalu',
            'Status',
        ];

        $startRow = 5;
        foreach ($headers as $colIdx => $headerText) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue($colLetter.$startRow, $headerText);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex(count($headers));

        // Format Header Baris
        $sheet->getRowDimension($startRow)->setRowHeight(28);
        $sheet->getStyle("A{$startRow}:{$lastColLetter}{$startRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0F172A'],
                ],
            ],
        ]);

        // 3. Baris Data
        $currentRow = $startRow + 1;
        $no = 1;
        $currencyFormat = '"Rp" #,##0;("Rp" #,##0);"-"';

        $exportSellerIds = $sellers->pluck('id');
        $exportMetrics = $exportSellerIds->isEmpty() ? collect() : Order::whereIn('seller_id', $exportSellerIds)
            ->selectRaw("
                seller_id,
                COUNT(*) as total_orders,
                COUNT(CASE WHEN created_at >= ? THEN 1 END) as today_closing,
                COALESCE(SUM(CASE WHEN created_at >= ? AND payment_status = 'verified' THEN grand_total ELSE 0 END), 0) as month_sales,
                COALESCE(SUM(CASE WHEN created_at >= ? AND created_at <= ? AND payment_status = 'verified' THEN grand_total ELSE 0 END), 0) as last_month_sales
            ", [$startOfToday, $startOfMonth, $startOfLastMonth, $endOfLastMonth])
            ->groupBy('seller_id')
            ->get()
            ->keyBy('seller_id');

        foreach ($sellers as $s) {
            $metric = $exportMetrics->get($s->id);
            $todayClosing = (int) ($metric->today_closing ?? 0);
            $totalOrdersCount = (int) ($metric->total_orders ?? 0);
            $monthSales = (float) ($metric->month_sales ?? 0);
            $lastMonthSales = (float) ($metric->last_month_sales ?? 0);

            $sheet->getRowDimension($currentRow)->setRowHeight(22);

            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValueExplicit("B{$currentRow}", (string) $s->seller_code, DataType::TYPE_STRING);
            $sheet->setCellValue("C{$currentRow}", $s->name);
            $sheet->setCellValue("D{$currentRow}", $s->brand ? $s->brand->name : '-');
            $sheet->setCellValueExplicit("E{$currentRow}", (string) $s->phone, DataType::TYPE_STRING);
            $sheet->setCellValue("F{$currentRow}", $todayClosing);
            $sheet->setCellValue("G{$currentRow}", $totalOrdersCount);
            $sheet->setCellValue("H{$currentRow}", $monthSales);
            $sheet->setCellValue("I{$currentRow}", $lastMonthSales);
            $sheet->setCellValue("J{$currentRow}", $s->is_active ? 'AKTIF' : 'NONAKTIF');

            if ($no % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFill()->applyFromArray([
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8FAFC'],
                ]);
            }

            $currentRow++;
        }

        $endDataRow = $currentRow - 1;

        if ($endDataRow >= $startRow + 1) {
            $sheet->getStyle('A'.($startRow + 1).":{$lastColLetter}{$endDataRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $sheet->getStyle('A'.($startRow + 1).":A{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.($startRow + 1).":B{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.($startRow + 1).":G{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J'.($startRow + 1).":J{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Angka & Mata Uang
            $sheet->getStyle('F'.($startRow + 1).":G{$endDataRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('H'.($startRow + 1).":I{$endDataRow}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle('H'.($startRow + 1).":I{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // 4. Baris Total Ringkasan
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $sheet->mergeCells("A{$currentRow}:E{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", 'TOTAL KESELURUHAN');
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue("F{$currentRow}", '=SUM(F'.($startRow + 1).":F{$endDataRow})");
            $sheet->setCellValue("G{$currentRow}", '=SUM(G'.($startRow + 1).":G{$endDataRow})");
            $sheet->setCellValue("H{$currentRow}", '=SUM(H'.($startRow + 1).":H{$endDataRow})");
            $sheet->setCellValue("I{$currentRow}", '=SUM(I'.($startRow + 1).":I{$endDataRow})");

            $sheet->getStyle("F{$currentRow}:G{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F{$currentRow}:G{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle("H{$currentRow}:I{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1F5F9'],
                ],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0F172A']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']],
                ],
            ]);
        }

        // 5. Auto-fit Lebar Kolom
        foreach (range(1, count($headers)) as $colIdx) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $fileName = 'Laporan-Performa-Mitra-PasarKita-'.date('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
