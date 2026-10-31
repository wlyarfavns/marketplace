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

        $orders = $query->latest()->paginate(15)->withQueryString();
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

    // Export Laporan Pesanan ke CSV / Excel
    public function exportExcel(Request $request)
    {
        $fileName = 'Laporan-Pesanan-PasarKita-'.date('Y-m-d').'.csv';
        $orders = Order::with(['seller', 'items'])->latest()->get();

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'No. Pesanan', 'Tanggal', 'Penerima', 'WhatsApp', 'Kota',
            'Skema Bayar', 'Subtotal', 'Diskon', 'Grand Total',
            'Nominal DP', 'Sisa Tagihan', 'Status Pesanan', 'Status Bayar', 'ID Seller Mitra',
        ];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
            fputcsv($file, $columns);

            foreach ($orders as $o) {
                fputcsv($file, [
                    $o->order_number,
                    $o->created_at->format('d/m/Y H:i'),
                    $o->recipient_first_name.' '.$o->recipient_last_name,
                    $o->whatsapp,
                    $o->city,
                    strtoupper($o->payment_method_type),
                    $o->subtotal,
                    $o->discount,
                    $o->grand_total,
                    $o->dp_amount,
                    $o->remaining_amount,
                    strtoupper($o->order_status),
                    strtoupper($o->payment_status),
                    $o->seller ? $o->seller->seller_code : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
