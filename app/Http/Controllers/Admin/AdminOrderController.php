<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\PaymentConfirmation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'seller.brand', 'items.product', 'paymentConfirmations']);

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'like', "%{$keyword}%")
                    ->orWhere('recipient_first_name', 'like', "%{$keyword}%")
                    ->orWhere('recipient_last_name', 'like', "%{$keyword}%")
                    ->orWhere('whatsapp', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method_type')) {
            $query->where('payment_method_type', $request->payment_method_type);
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        $orders = $query->latest()->paginate(8)->withQueryString();
        $brands = Brand::active()->get();

        return view('admin.orders.index', compact('orders', 'brands'));
    }

    public function show($id)
    {
        $order = Order::with([
            'user', 'seller.brand', 'coupon',
            'items.product', 'paymentConfirmations.bankAccount',
            'invoice',
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,completed,cancelled',
            'payment_status' => 'required|in:unpaid,waiting_verification,verified,rejected',
            'dp_status' => 'required|in:none,dp_paid,fully_paid',
        ]);

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
            'dp_status' => $request->dp_status,
        ]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function verifyPayment(Request $request, $orderId, $confirmationId)
    {
        $order = Order::findOrFail($orderId);
        $confirmation = PaymentConfirmation::where('order_id', $order->id)->findOrFail($confirmationId);

        $status = $request->input('action') === 'verify' ? 'verified' : 'rejected';

        $confirmation->update([
            'verification_status' => $status,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'notes' => $request->input('notes'),
        ]);

        if ($status === 'verified') {
            if ($confirmation->payment_stage === 'dp') {
                $order->update([
                    'payment_status' => 'verified',
                    'dp_status' => 'dp_paid',
                    'order_status' => 'processing',
                ]);
            } else {
                $order->update([
                    'payment_status' => 'verified',
                    'dp_status' => 'fully_paid',
                    'order_status' => 'processing',
                ]);
            }
        } else {
            $order->update(['payment_status' => 'rejected']);
        }

        return redirect()->back()->with('success', "Verifikasi pembayaran berhasil diperbarui ke: {$status}.");
    }

    // Generate dan Download Faktur PDF Resmi
    public function generateInvoicePdf($id)
    {
        $order = Order::with(['items.product', 'seller', 'coupon', 'paymentConfirmations.bankAccount'])->findOrFail($id);

        $invoiceNumber = 'FKT-'.date('Ymd').'-'.str_pad($order->id, 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::firstOrCreate(
            ['order_id' => $order->id],
            [
                'invoice_number' => $invoiceNumber,
                'created_by' => Auth::id(),
            ]
        );

        $pdf = Pdf::loadView('admin.orders.invoice-pdf', compact('order', 'invoice'));

        return $pdf->download("{$invoice->invoice_number}.pdf");
    }

    // Export Laporan Pesanan Resmi ke Microsoft Excel (.XLSX)
    public function exportExcel(Request $request)
    {
        $query = Order::with(['seller', 'items']);

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'like', "%{$keyword}%")
                    ->orWhere('recipient_first_name', 'like', "%{$keyword}%")
                    ->orWhere('recipient_last_name', 'like', "%{$keyword}%")
                    ->orWhere('whatsapp', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method_type')) {
            $query->where('payment_method_type', $request->payment_method_type);
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        $orders = $query->latest()->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Pesanan');

        // 1. Header Judul Laporan
        $sheet->setCellValue('A1', 'FOOTWORK — TOKO SEPATU BRANDED & SNEAKERS ORIGINAL');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('0F172A'));

        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI TRANSAKSI & PESANAN');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new Color('334155'));

        $sheet->setCellValue('A3', 'Dicetak: '.now()->format('d/m/Y H:i').' WIB | Total Data: '.$orders->count().' Transaksi');
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9)->setColor(new Color('64748B'));

        // 2. Baris Header Tabel
        $headers = [
            'No.',
            'No. Pesanan',
            'Tanggal',
            'Nama Penerima',
            'No. WhatsApp',
            'Kota / Domisili',
            'Kurir',
            'Skema Bayar',
            'Subtotal',
            'Diskon',
            'Ongkir',
            'Grand Total',
            'Nominal DP',
            'Sisa Tagihan',
            'Status Pesanan',
            'Status Bayar',
            'ID Seller Mitra',
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

        foreach ($orders as $o) {
            $sheet->getRowDimension($currentRow)->setRowHeight(22);

            $sheet->setCellValue("A{$currentRow}", $no++);
            $sheet->setCellValueExplicit("B{$currentRow}", (string) $o->order_number, DataType::TYPE_STRING);
            $sheet->setCellValue("C{$currentRow}", $o->created_at ? $o->created_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValue("D{$currentRow}", trim($o->recipient_first_name.' '.$o->recipient_last_name));
            $sheet->setCellValueExplicit("E{$currentRow}", (string) $o->whatsapp, DataType::TYPE_STRING);
            $sheet->setCellValue("F{$currentRow}", $o->city ?: '-');
            $sheet->setCellValue("G{$currentRow}", $o->courier_name ?: '-');
            $sheet->setCellValue("H{$currentRow}", strtoupper($o->payment_method_type ?: 'FULL'));
            $sheet->setCellValue("I{$currentRow}", (float) $o->subtotal);
            $sheet->setCellValue("J{$currentRow}", (float) $o->discount);
            $sheet->setCellValue("K{$currentRow}", (float) $o->shipping_cost);
            $sheet->setCellValue("L{$currentRow}", (float) $o->grand_total);
            $sheet->setCellValue("M{$currentRow}", (float) $o->dp_amount);
            $sheet->setCellValue("N{$currentRow}", (float) $o->remaining_amount);
            $sheet->setCellValue("O{$currentRow}", strtoupper($o->order_status));
            $sheet->setCellValue("P{$currentRow}", strtoupper($o->payment_status));
            $sheet->setCellValue("Q{$currentRow}", $o->seller ? $o->seller->seller_code : '-');

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
            $sheet->getStyle('B'.($startRow + 1).":C{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E'.($startRow + 1).":E{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G'.($startRow + 1).":H{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O'.($startRow + 1).":Q{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format Mata Uang Kolom I sampai N
            $sheet->getStyle('I'.($startRow + 1).":N{$endDataRow}")->getNumberFormat()->setFormatCode($currencyFormat);
            $sheet->getStyle('I'.($startRow + 1).":N{$endDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // 4. Baris Total Ringkasan
            $sheet->getRowDimension($currentRow)->setRowHeight(25);
            $sheet->mergeCells("A{$currentRow}:H{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", 'TOTAL KESELURUHAN');
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->setCellValue("I{$currentRow}", '=SUM(I'.($startRow + 1).":I{$endDataRow})");
            $sheet->setCellValue("J{$currentRow}", '=SUM(J'.($startRow + 1).":J{$endDataRow})");
            $sheet->setCellValue("K{$currentRow}", '=SUM(K'.($startRow + 1).":K{$endDataRow})");
            $sheet->setCellValue("L{$currentRow}", '=SUM(L'.($startRow + 1).":L{$endDataRow})");
            $sheet->setCellValue("M{$currentRow}", '=SUM(M'.($startRow + 1).":M{$endDataRow})");
            $sheet->setCellValue("N{$currentRow}", '=SUM(N'.($startRow + 1).":N{$endDataRow})");

            $sheet->getStyle("I{$currentRow}:N{$currentRow}")->getNumberFormat()->setFormatCode($currencyFormat);
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

        $fileName = 'Laporan-Pesanan-PasarKita-'.date('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
