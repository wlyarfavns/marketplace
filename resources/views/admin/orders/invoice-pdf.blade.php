<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>FAKTUR RESMI — {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #111;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #111;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .invoice-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding: 3px 0;
        }
        .items-table {
            margin-top: 20px;
            border: 1px solid #ddd;
        }
        .items-table th {
            background-color: #f7f7f7;
            border-bottom: 1px solid #111;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 8px 6px;
            border-bottom: 1px solid #eee;
        }
        .total-section {
            margin-top: 20px;
            width: 45%;
            margin-left: auto;
        }
        .total-section td {
            padding: 4px 0;
        }
        .grand-total {
            font-size: 13px;
            font-weight: bold;
            border-top: 1px solid #111;
            border-bottom: 2px solid #111;
            padding: 6px 0 !important;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 9px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Faktur -->
    <table class="header">
        <tr>
            <td style="width: 50%;">
                <div class="logo">PASARKITA</div>
                <div style="color: #666; font-size: 10px; margin-top: 4px;">
                    Toko Sepatu Branded & Sneakers Original Terpercaya<br>
                    Website: pasarkita.com &bull; CS WhatsApp Resmi
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="invoice-title">FAKTUR RESMI PEMBELIAN</div>
                <div style="font-size: 12px; font-weight: bold; margin-top: 3px;">{{ $invoice->invoice_number }}</div>
                <div style="color: #555; font-size: 10px; margin-top: 2px;">
                    Tanggal Faktur: {{ $invoice->created_at->format('d/m/Y') }}<br>
                    No. Order: <strong>{{ $order->order_number }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Pembeli & Pengiriman -->
    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <strong>DITUJUKAN KEPADA:</strong><br>
                {{ $order->recipient_first_name }} {{ $order->recipient_last_name }}<br>
                WhatsApp: {{ $order->whatsapp }}<br>
                {{ $order->address }}<br>
                {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>DETAIL TRANSAKSI:</strong><br>
                Metode: {{ $order->payment_method_type === 'dp' ? 'Down Payment (DP)' : 'Pembayaran Penuh (100%)' }}<br>
                Ekspedisi: {{ $order->courier_name }}<br>
                Mitra Seller: {{ $order->seller ? $order->seller->name . ' (' . $order->seller->seller_code . ')' : 'Mandiri' }}<br>
                Status Pembayaran: <strong>{{ strtoupper($order->payment_status) }}</strong>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian Item -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">Deskripsi Produk & Varian</th>
                <th style="width: 15%; text-align: right;">Harga Satuan</th>
                <th style="width: 10%; text-align: center;">Jumlah</th>
                <th style="width: 25%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $idx => $item)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->product_name_snapshot }}</strong><br>
                        <span style="color: #666; font-size: 10px;">
                            Model: {{ $item->model_name }} | Warna: {{ $item->color_name }} | Ukuran: {{ $item->size_name }}
                        </span>
                        @if($item->bonus_item_snapshot)
                            <br><span style="color: #16A34A; font-size: 9px; font-weight: bold;">Bonus Gratis: {{ $item->bonus_item_snapshot }}</span>
                        @endif
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Ringkasan Total & Skema DP -->
    <table class="total-section">
        <tr>
            <td>Subtotal Belanja</td>
            <td style="text-align: right; font-weight: bold;">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount > 0)
            <tr>
                <td style="color: #16A34A;">Potongan Diskon Kupon</td>
                <td style="text-align: right; color: #16A34A; font-weight: bold;">- Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td>Ongkos Kirim ({{ $order->courier_name }})</td>
            <td style="text-align: right;">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand-total">
            <td>GRAND TOTAL</td>
            <td style="text-align: right;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
        </tr>

        @if($order->payment_method_type === 'dp')
            <tr>
                <td style="font-weight: bold; padding-top: 6px;">Uang Muka (DP) Terbayar:</td>
                <td style="text-align: right; font-weight: bold; padding-top: 6px;">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="color: #d97706;">Sisa Tagihan Pelunasan:</td>
                <td style="text-align: right; color: #d97706; font-weight: bold;">Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    <!-- Footer Dokumen -->
    <div class="footer">
        Faktur ini diterbitkan secara sah oleh sistem PasarKita dan berlaku sebagai bukti transaksi resmi.<br>
        Terima kasih atas kepercayaan Anda berbelanja sepatu branded original di PasarKita.
    </div>

</body>
</html>

