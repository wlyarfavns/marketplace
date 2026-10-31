@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-8 flex justify-between items-center">
        <div>
            <span class="text-xs uppercase tracking-widest text-black font-bold">Rincian Lengkap Pesanan</span>
            <h1 class="text-3xl font-serif font-black text-gray-950 mt-1">{{ $order->order_number }}</h1>
        </div>
        <a href="{{ route('account.orders') }}" class="inline-flex items-center gap-2 border-2 border-gray-300 bg-white px-4 py-2 rounded text-xs uppercase font-bold text-gray-700 hover:text-black hover:border-black transition shadow-2xs active:translate-y-0.5 cursor-pointer">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-8 py-12 space-y-8">
    
    <!-- Status Card -->
    <div class="p-6 border-2 border-gray-200 rounded-lg bg-white grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs shadow-2xs">
        <div>
            <span class="text-gray-400 uppercase font-bold block text-[10px]">Tanggal Pemesanan</span>
            <p class="font-bold text-gray-900 mt-1">{{ $order->created_at->format('d F Y, H:i') }}</p>
        </div>
        <div>
            <span class="text-gray-400 uppercase font-bold block text-[10px]">Status Pesanan</span>
            <p class="font-bold uppercase text-gray-900 mt-1">
                @if($order->order_status === 'completed')
                    <span class="text-black font-black">Selesai</span>
                @elseif($order->order_status === 'processing')
                    <span class="text-blue-700 font-black">Diproses</span>
                @elseif($order->order_status === 'shipped')
                    <span class="text-gray-900 font-black">Dikirim</span>
                @else
                    <span class="text-amber-700 font-black">Menunggu</span>
                @endif
            </p>
        </div>
        <div>
            <span class="text-gray-400 uppercase font-bold block text-[10px]">Status Pembayaran</span>
            <p class="font-bold uppercase text-gray-900 mt-1">
                @if($order->payment_status === 'verified')
                    <span class="text-black font-black">Terverifikasi</span>
                @elseif($order->payment_status === 'waiting_verification')
                    <span class="text-blue-700 font-black">Sedang Diverifikasi</span>
                @else
                    <span class="text-amber-700 font-black">Belum Lunas</span>
                @endif
            </p>
        </div>
        <div>
            <span class="text-gray-400 uppercase font-bold block text-[10px]">Metode Pembayaran</span>
            <p class="font-bold uppercase text-gray-900 mt-1">{{ $order->payment_method_type === 'dp' ? 'Down Payment (DP)' : 'Bayar Penuh 100%' }}</p>
        </div>
    </div>

    <!-- Rincian Produk -->
    <div class="border-2 border-gray-200 rounded-lg bg-white p-6 space-y-4 shadow-2xs">
        <h3 class="text-xs uppercase font-bold tracking-wider text-gray-900 border-b border-gray-100 pb-3">Produk Yang Dipesan</h3>
        <div class="divide-y divide-gray-100 text-xs">
            @foreach($order->items as $item)
                <div class="py-3 flex justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-14 bg-neutral-100 border border-gray-200 rounded shrink-0 overflow-hidden flex items-center justify-center">
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
                                <span class="text-[10px] text-black bg-neutral-100 px-2 py-0.5 border border-gray-300 rounded inline-block mt-1 font-bold">
                                    Hadiah: {{ $item->bonus_item_snapshot }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-gray-500 font-medium">{{ $item->quantity }} &times; Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        <p class="font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-gray-200 space-y-2 text-xs">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span class="font-bold text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between text-black font-bold">
                    <span>Diskon Kupon</span>
                    <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-gray-600">
                <span>Ongkos Kirim ({{ $order->courier_name }})</span>
                <span class="font-bold text-green-700">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }} (Promo)</span>
            </div>
            <div class="flex justify-between text-sm font-black text-gray-950 pt-2 border-t border-gray-100">
                <span>Grand Total</span>
                <span class="text-base font-black">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>

            @if($order->payment_method_type === 'dp')
                <div class="mt-3 p-3.5 bg-gray-50 border-2 border-gray-300 rounded-lg space-y-1.5 shadow-2xs">
                    <div class="flex justify-between font-black text-gray-950">
                        <span>Tagihan DP:</span>
                        <span class="text-black">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[11px] text-gray-600 font-medium">
                        <span>Sisa Pelunasan:</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Alamat Pengiriman & Atribusi Mitra -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
        <div class="p-6 border-2 border-gray-200 rounded-lg bg-white space-y-2 shadow-2xs">
            <h4 class="font-bold uppercase tracking-wider text-gray-900">Informasi Penerima</h4>
            <p class="font-bold text-gray-900">{{ $order->recipient_first_name }} {{ $order->recipient_last_name }}</p>
            <p class="text-gray-500">WhatsApp: {{ $order->whatsapp }}</p>
            <p class="text-gray-500 leading-relaxed">{{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
        </div>

        <div class="p-6 border-2 border-gray-200 rounded-lg bg-white space-y-2 shadow-2xs">
            <h4 class="font-bold uppercase tracking-wider text-gray-900">Atribusi Kemitraan & Ekspedisi</h4>
            <p class="text-gray-600">Ekspedisi: <span class="font-bold text-gray-900">{{ $order->courier_name }}</span></p>
            <p class="text-gray-600">
                Mitra Penjual: 
                <span class="font-bold text-gray-900">
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
        <a href="{{ route('checkout.success', $order->order_number) }}" class="inline-flex items-center gap-2 border-2 border-black bg-black text-white px-8 py-3.5 text-xs uppercase tracking-widest font-black hover:bg-neutral-800 transition shadow-md rounded-lg active:translate-y-0.5 cursor-pointer">
            <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
            <span>Unggah / Periksa Bukti Transfer Bank</span>
        </a>
    </div>

</div>
@endsection
