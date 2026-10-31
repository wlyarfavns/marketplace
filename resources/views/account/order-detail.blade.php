@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-8 flex justify-between items-center">
        <div>
            <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">Rincian Lengkap Pesanan</span>
            <h1 class="text-3xl font-serif font-bold text-gray-950 mt-1">{{ $order->order_number }}</h1>
        </div>
        <a href="{{ route('account.orders') }}" class="text-xs uppercase font-semibold text-gray-600 hover:text-black">
            &larr; Kembali ke Daftar
        </a>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-8 py-12 space-y-8">
    
    <!-- Status Card -->
    <div class="p-6 border border-gray-200 bg-white grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="text-gray-400 uppercase font-semibold block text-[10px]">Tanggal Pemesanan</span>
            <p class="font-bold text-gray-900 mt-1">{{ $order->created_at->format('d F Y, H:i') }}</p>
        </div>
        <div>
            <span class="text-gray-400 uppercase font-semibold block text-[10px]">Status Pesanan</span>
            <p class="font-bold uppercase text-gray-900 mt-1">{{ $order->order_status }}</p>
        </div>
        <div>
            <span class="text-gray-400 uppercase font-semibold block text-[10px]">Status Pembayaran</span>
            <p class="font-bold uppercase text-gray-900 mt-1">{{ $order->payment_status }}</p>
        </div>
        <div>
            <span class="text-gray-400 uppercase font-semibold block text-[10px]">Metode Pembayaran</span>
            <p class="font-bold uppercase text-gray-900 mt-1">{{ $order->payment_method_type === 'dp' ? 'Down Payment (DP)' : 'Bayar Penuh 100%' }}</p>
        </div>
    </div>

    <!-- Rincian Produk -->
    <div class="border border-gray-200 bg-white p-6 space-y-4">
        <h3 class="text-xs uppercase font-bold tracking-wider text-gray-900 border-b border-gray-100 pb-3">Produk Yang Dipesan</h3>
        <div class="divide-y divide-gray-100 text-xs">
            @foreach($order->items as $item)
                <div class="py-3 flex justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-14 bg-neutral-100 border border-gray-200 shrink-0 overflow-hidden flex items-center justify-center">
                            @if($item->product && $item->product->primary_image_url)
                                <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name_snapshot }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-shoe-prints text-xs text-gray-400"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $item->product_name_snapshot }}</p>
                            <p class="text-gray-500 text-[11px] mt-0.5">
                                Model: {{ $item->model_name }} &bull; Warna: {{ $item->color_name }} &bull; Ukuran: {{ $item->size_name }}
                            </p>
                            @if($item->bonus_item_snapshot)
                                <span class="text-[10px] text-black bg-neutral-100 px-1.5 py-0.5 border border-gray-300 inline-block mt-1 font-medium">
                                    Hadiah: {{ $item->bonus_item_snapshot }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-gray-500">{{ $item->quantity }} &times; Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        <p class="font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-gray-200 space-y-2 text-xs">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between text-black font-semibold">
                    <span>Diskon Kupon</span>
                    <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-gray-600">
                <span>Ongkos Kirim ({{ $order->courier_name }})</span>
                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold text-gray-950 pt-2 border-t border-gray-100">
                <span>Grand Total</span>
                <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>

            @if($order->payment_method_type === 'dp')
                <div class="mt-3 p-3 bg-gray-50 border border-gray-200 space-y-1">
                    <div class="flex justify-between font-bold text-gray-900">
                        <span>Tagihan DP:</span>
                        <span>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[11px] text-gray-500">
                        <span>Sisa Pelunasan:</span>
                        <span>Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alamat Pengiriman & Atribusi Mitra -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
        <div class="p-6 border border-gray-200 bg-white space-y-2">
            <h4 class="font-bold uppercase tracking-wider text-gray-900">Informasi Penerima</h4>
            <p class="font-medium text-gray-800">{{ $order->recipient_first_name }} {{ $order->recipient_last_name }}</p>
            <p class="text-gray-500">WhatsApp: {{ $order->whatsapp }}</p>
            <p class="text-gray-500 leading-relaxed">{{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
        </div>

        <div class="p-6 border border-gray-200 bg-white space-y-2">
            <h4 class="font-bold uppercase tracking-wider text-gray-900">Atribusi Kemitraan & Ekspedisi</h4>
            <p class="text-gray-600">Ekspedisi: <span class="font-medium text-gray-900">{{ $order->courier_name }}</span></p>
            <p class="text-gray-600">
                Mitra Penjual: 
                <span class="font-medium text-gray-900">
                    {{ $order->seller ? $order->seller->name . ' (' . $order->seller->seller_code . ')' : 'Transaksi Mandiri' }}
                </span>
            </p>
            @if($order->customer_notes)
                <p class="text-gray-500 italic mt-2">"{{ $order->customer_notes }}"</p>
            @endif
        </div>
    </div>

    <!-- Tombol Upload Bukti Transfer -->
    <div class="text-center pt-4">
        <a href="{{ route('checkout.success', $order->order_number) }}" class="inline-block bg-black text-white px-8 py-3.5 text-xs uppercase tracking-widest font-semibold hover:bg-gray-800 transition">
            Unggah / Periksa Bukti Transfer Bank
        </a>
    </div>

</div>
@endsection

