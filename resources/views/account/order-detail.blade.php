@extends('layouts.app')

@section('content')
<div class="border-b border-neutral-200 bg-neutral-50/80 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-neutral-500 uppercase tracking-widest font-semibold mb-1">
                <a href="{{ route('account.dashboard') }}" class="hover:text-black">Akun Saya</a>
                <span>/</span>
                <a href="{{ route('account.orders') }}" class="hover:text-black">Riwayat Pemesanan</a>
                <span>/</span>
                <span class="text-neutral-900 font-mono">{{ $order->order_number }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">Detail Pesanan</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('account.orders') }}" class="px-4 py-2 border border-neutral-300 hover:border-black text-neutral-700 hover:text-black text-xs font-bold uppercase tracking-wider rounded-xl transition">
                &larr; Kembali
            </a>
            @if($order->payment_status !== 'verified')
                <a href="{{ route('checkout.success', $order->order_number) }}" class="px-4 py-2 bg-neutral-950 hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-xs">
                    Unggah Bukti Bayar
                </a>
            @endif
        </div>
    </div>
</div>

@include('account.partials.nav')

<div class="max-w-5xl mx-auto px-4 sm:px-8 pb-16 space-y-8">
    
    <!-- Status Utama Pesanan -->
    <div class="p-6 bg-white border border-neutral-200 rounded-2xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 text-xs shadow-xs">
        <div>
            <span class="text-neutral-400 uppercase font-bold text-[10px] tracking-wider block mb-1">Waktu Pemesanan</span>
            <p class="font-extrabold text-neutral-950 text-sm">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
            <span class="text-[11px] text-neutral-500 font-mono">{{ $order->order_number }}</span>
        </div>
        <div>
            <span class="text-neutral-400 uppercase font-bold text-[10px] tracking-wider block mb-1">Status Pesanan</span>
            <span class="inline-block px-3 py-1 rounded-md text-xs font-bold uppercase border {{ $order->status_badge }}">
                <i class="fa-solid fa-circle-dot mr-1 text-[8px]"></i> {{ $order->order_status_label }}
            </span>
        </div>
        <div>
            <span class="text-neutral-400 uppercase font-bold text-[10px] tracking-wider block mb-1">Status Pembayaran</span>
            <span class="inline-block px-3 py-1 rounded-md text-xs font-bold uppercase border {{ $order->payment_status_badge }}">
                {{ $order->payment_status_label }}
            </span>
        </div>
        <div>
            <span class="text-neutral-400 uppercase font-bold text-[10px] tracking-wider block mb-1">Skema Transaksi</span>
            <p class="font-extrabold text-neutral-950 text-sm">
                {{ $order->payment_method_type === 'dp' ? 'Down Payment (DP)' : 'Bayar Penuh 100%' }}
            </p>
            <span class="text-[11px] text-neutral-500">Transfer Bank Manual</span>
        </div>
    </div>

    <!-- Rincian Produk Snapshot -->
    <div class="bg-white border border-neutral-200 rounded-2xl p-6 space-y-4 shadow-xs">
        <div class="flex justify-between items-center border-b border-neutral-100 pb-3">
            <h3 class="text-xs uppercase font-extrabold tracking-wider text-neutral-900 flex items-center gap-2">
                <i class="fa-solid fa-box text-neutral-400"></i> Daftar Produk Yang Dipesan
            </h3>
            <span class="text-xs text-neutral-500 font-semibold">{{ $order->items->sum('quantity') }} Total Pasang</span>
        </div>

        <div class="divide-y divide-neutral-100 text-xs">
            @foreach($order->items as $item)
                <div class="py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-xl bg-neutral-100 border border-neutral-200 shrink-0 overflow-hidden flex items-center justify-center">
                            @if($item->product && $item->product->primary_image_url)
                                <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name_snapshot }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-shoe-prints text-sm text-neutral-300"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-neutral-950 text-sm">{{ $item->product_name_snapshot }}</h4>
                            <p class="text-neutral-500 text-xs mt-0.5">
                                @if($item->model_name) Model: <span class="text-neutral-800">{{ $item->model_name }}</span> &bull; @endif
                                @if($item->color_name) Warna: <span class="text-neutral-800">{{ $item->color_name }}</span> &bull; @endif
                                @if($item->size_name) Ukuran: <span class="font-bold text-neutral-950">{{ $item->size_name }}</span> @endif
                            </p>
                            @if($item->bonus_item_snapshot)
                                <span class="inline-flex items-center gap-1 text-[10px] text-neutral-800 bg-neutral-100 border border-neutral-300 px-2 py-0.5 rounded-md mt-1.5 font-semibold">
                                    <i class="fa-solid fa-gift text-neutral-500 text-[9px]"></i> Bonus: {{ $item->bonus_item_snapshot }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-left sm:text-right shrink-0">
                        <p class="text-neutral-500 text-xs">{{ $item->quantity }} &times; Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        <p class="font-extrabold text-neutral-950 text-sm mt-0.5">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Rincian Biaya & Perhitungan -->
        <div class="pt-5 border-t border-neutral-200 space-y-2 text-xs">
            <div class="flex justify-between text-neutral-600">
                <span>Subtotal Produk</span>
                <span class="font-medium text-neutral-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between text-green-700 font-semibold">
                    <span>Diskon Kupon Promo</span>
                    <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-neutral-600">
                <span>Biaya Pengiriman ({{ $order->courier_name }})</span>
                <span class="font-medium text-neutral-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-base font-extrabold text-neutral-950 pt-3 border-t border-neutral-100">
                <span>Grand Total Pembayaran</span>
                <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>

            @if($order->payment_method_type === 'dp')
                <div class="mt-4 p-4 bg-neutral-50 border border-neutral-200 rounded-xl space-y-1.5">
                    <div class="flex justify-between font-bold text-neutral-900 text-xs">
                        <span>Kewajiban Uang Muka (DP):</span>
                        <span>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[11px] text-neutral-500">
                        <span>Sisa Pelunasan Sebelum Kirim:</span>
                        <span>Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Riwayat Transaksi Pembayaran Terkait Pesanan Ini -->
    <div class="bg-white border border-neutral-200 rounded-2xl p-6 space-y-4 shadow-xs">
        <div class="flex justify-between items-center border-b border-neutral-100 pb-3">
            <h3 class="text-xs uppercase font-extrabold tracking-wider text-neutral-900 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-neutral-400"></i> Riwayat Transaksi Pembayaran ({{ $order->paymentConfirmations->count() }})
            </h3>
            <a href="{{ route('account.transactions') }}" class="text-xs font-semibold text-neutral-500 hover:text-black">
                Lihat Semua Transaksi &rarr;
            </a>
        </div>

        @if($order->paymentConfirmations->isEmpty())
            <div class="py-8 text-center bg-neutral-50 rounded-xl border border-dashed border-neutral-200 text-xs text-neutral-500">
                <p>Belum ada bukti pembayaran yang dikirimkan untuk pesanan ini.</p>
                <a href="{{ route('checkout.success', $order->order_number) }}" class="inline-block mt-3 px-4 py-2 bg-neutral-900 hover:bg-neutral-800 text-white font-bold rounded-lg transition">
                    Unggah Bukti Transfer Sekarang
                </a>
            </div>
        @else
            <div class="divide-y divide-neutral-100 text-xs">
                @foreach($order->paymentConfirmations as $pc)
                    <div class="py-3.5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('account.transaction.detail', $pc->id) }}" class="font-mono font-bold text-neutral-950 hover:underline">
                                    {{ $pc->transaction_number }}
                                </a>
                                <span class="text-neutral-300">|</span>
                                <span class="text-[11px] text-neutral-500">{{ $pc->created_at->format('d M Y, H:i') }} WIB</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-neutral-100 text-neutral-700">
                                    {{ $pc->stage_label }}
                                </span>
                            </div>
                            <p class="text-neutral-500 text-[11px]">
                                Tujuan: <strong class="text-neutral-800">{{ $pc->bankAccount ? $pc->bankAccount->bank_name . ' (' . $pc->bankAccount->account_number . ')' : 'Bank Transfer' }}</strong>
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-left sm:text-right">
                                <span class="text-[10px] uppercase font-bold text-neutral-400 block">Nominal</span>
                                <span class="font-extrabold text-neutral-950 text-sm">Rp {{ number_format($pc->amount, 0, ',', '.') }}</span>
                            </div>
                            <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded-md border {{ $pc->status_badge }}">
                                {{ $pc->verification_status }}
                            </span>
                            <a href="{{ route('account.transaction.detail', $pc->id) }}" class="px-3 py-1.5 border border-neutral-300 hover:border-black text-neutral-800 text-[11px] font-bold rounded-lg transition">
                                Detail Transaksi
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Alamat Pengiriman & Informasi Mitra -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
        <div class="p-6 border border-neutral-200 bg-white rounded-2xl space-y-2 shadow-xs">
            <h4 class="font-extrabold uppercase tracking-wider text-neutral-900 flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-neutral-400"></i> Informasi Penerima
            </h4>
            <p class="font-bold text-neutral-950 text-sm">{{ $order->recipient_first_name }} {{ $order->recipient_last_name }}</p>
            <p class="text-neutral-600">WhatsApp: <span class="font-medium text-neutral-900">{{ $order->whatsapp }}</span></p>
            <p class="text-neutral-500 leading-relaxed pt-1">
                {{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}
            </p>
        </div>

        <div class="p-6 border border-neutral-200 bg-white rounded-2xl space-y-2 shadow-xs">
            <h4 class="font-extrabold uppercase tracking-wider text-neutral-900 flex items-center gap-2">
                <i class="fa-solid fa-truck-fast text-neutral-400"></i> Ekspedisi & Catatan
            </h4>
            <p class="text-neutral-600">Jasa Pengiriman: <span class="font-bold text-neutral-900">{{ $order->courier_name }}</span></p>
            <p class="text-neutral-600">
                Mitra Terkait: 
                <span class="font-semibold text-neutral-900">
                    {{ $order->seller ? $order->seller->name . ' (' . $order->seller->seller_code . ')' : 'Transaksi Toko Mandiri' }}
                </span>
            </p>
            @if($order->customer_notes)
                <div class="mt-2 p-3 bg-neutral-50 rounded-xl border border-neutral-100 text-neutral-600 italic">
                    "{{ $order->customer_notes }}"
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
