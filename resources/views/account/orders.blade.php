@extends('layouts.app')

@section('content')
<div class="border-b border-neutral-200 bg-neutral-50/80 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-neutral-500 uppercase tracking-widest font-semibold mb-1">
                <a href="{{ route('account.dashboard') }}" class="hover:text-black">Akun Saya</a>
                <span>/</span>
                <span class="text-neutral-900">Riwayat Pemesanan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">Daftar Riwayat Pesanan</h1>
        </div>
        <a href="{{ route('catalog') }}" class="px-4 py-2 bg-neutral-950 hover:bg-neutral-800 text-white text-xs uppercase font-bold tracking-wider transition rounded-lg shadow-xs">
            + Belanja Lagi
        </a>
    </div>
</div>

@include('account.partials.nav')

<div class="max-w-7xl mx-auto px-4 sm:px-8 pb-16 space-y-6">

    <!-- Filter & Pencarian Bar -->
    <div class="bg-white border border-neutral-200 rounded-2xl p-5 shadow-xs">
        <form action="{{ route('account.orders') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Nomor Pesanan -->
                <div class="space-y-1 sm:col-span-2 lg:col-span-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">Cari Pesanan</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nomor pesanan (cth: ORD...)"
                               class="w-full text-xs px-3.5 py-2.5 pl-9 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                    </div>
                </div>

                <!-- Filter Status Pesanan -->
                <div class="space-y-1">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">Status Pesanan</label>
                    <select name="status" class="w-full text-xs px-3 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950 bg-white">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <!-- Filter Status Bayar -->
                <div class="space-y-1">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">Status Pembayaran</label>
                    <select name="payment_status" class="w-full text-xs px-3 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950 bg-white">
                        <option value="">Semua Pembayaran</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="waiting_verification" {{ request('payment_status') === 'waiting_verification' ? 'selected' : '' }}>Verifikasi Bukti</option>
                        <option value="dp_paid" {{ request('payment_status') === 'dp_paid' ? 'selected' : '' }}>DP Terbayar</option>
                        <option value="verified" {{ request('payment_status') === 'verified' ? 'selected' : '' }}>Terverifikasi (Lunas)</option>
                        <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Sort Urutan -->
                <div class="space-y-1">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">Urutkan</label>
                    <select name="sort" class="w-full text-xs px-3 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950 bg-white">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
            </div>

            <!-- Rentang Tanggal & Tombol Aksi -->
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 pt-2 border-t border-neutral-100 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider whitespace-nowrap">Rentang:</span>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="text-xs px-2.5 py-1.5 border border-neutral-200 rounded-lg focus:outline-none focus:border-neutral-950">
                    <span class="text-neutral-400">&ndash;</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="text-xs px-2.5 py-1.5 border border-neutral-200 rounded-lg focus:outline-none focus:border-neutral-950">
                </div>

                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to', 'sort']))
                        <a href="{{ route('account.orders') }}" class="px-3.5 py-2 text-neutral-600 hover:text-black text-xs font-semibold">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="px-5 py-2 bg-neutral-900 hover:bg-neutral-800 text-white font-bold text-xs rounded-xl shadow-xs cursor-pointer">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Daftar Pesanan Pelanggan -->
    @if($orders->isEmpty())
        <div class="text-center py-20 border border-dashed border-neutral-200 bg-white rounded-2xl p-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-neutral-100 flex items-center justify-center text-neutral-400">
                <i class="fa-solid fa-receipt text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-neutral-900">Tidak Ada Pesanan Ditemukan</h3>
            <p class="text-xs text-neutral-500 mt-1 max-w-md mx-auto">
                @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                    Tidak ada pesanan yang cocok dengan kriteria filter pencarian Anda. Coba ubah atau reset filter.
                @else
                    Anda belum pernah membuat pesanan di FOOTWORK. Temukan sneakers original favorit Anda sekarang!
                @endif
            </p>
            <div class="mt-5">
                @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to']))
                    <a href="{{ route('account.orders') }}" class="inline-block bg-neutral-900 text-white px-5 py-2.5 text-xs uppercase tracking-wider font-bold rounded-xl hover:bg-neutral-800 transition">
                        Reset Filter
                    </a>
                @else
                    <a href="{{ route('catalog') }}" class="inline-block bg-neutral-950 text-white px-6 py-2.5 text-xs uppercase tracking-widest font-bold rounded-xl hover:bg-neutral-800 transition shadow-xs">
                        Mulai Belanja
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white border border-neutral-200 rounded-2xl overflow-hidden shadow-xs hover:border-neutral-300 transition">
                    <!-- Header Kartu Pesanan -->
                    <div class="p-4 sm:p-5 bg-neutral-50/70 border-b border-neutral-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="font-mono font-extrabold text-neutral-950 text-sm tracking-tight">{{ $order->order_number }}</span>
                            <span class="text-neutral-300">|</span>
                            <span class="text-neutral-500 text-[11px]">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md bg-neutral-200/80 text-neutral-800">
                                {{ $order->payment_method_type === 'dp' ? 'Skema DP' : 'Bayar Penuh' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Badge Status Pesanan -->
                            <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded-md border {{ $order->status_badge }}">
                                <i class="fa-solid fa-circle-dot mr-1 text-[8px]"></i> {{ $order->order_status_label }}
                            </span>
                            <!-- Badge Status Bayar -->
                            <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded-md border {{ $order->payment_status_badge }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Daftar Item Produk dalam Pesanan -->
                    <div class="p-4 sm:p-6 divide-y divide-neutral-100">
                        @foreach($order->items as $item)
                            <div class="py-3.5 first:pt-0 last:pb-0 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-14 h-14 rounded-xl bg-neutral-100 border border-neutral-200 shrink-0 overflow-hidden flex items-center justify-center">
                                        @if($item->product && $item->product->primary_image_url)
                                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name_snapshot }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fa-solid fa-shoe-prints text-neutral-300 text-sm"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-neutral-950 text-sm leading-snug">{{ $item->product_name_snapshot }}</h4>
                                        <p class="text-neutral-500 text-[11px] mt-0.5">
                                            @if($item->model_name) Model: <span class="text-neutral-800">{{ $item->model_name }}</span> &bull; @endif
                                            @if($item->color_name) Warna: <span class="text-neutral-800">{{ $item->color_name }}</span> &bull; @endif
                                            @if($item->size_name) Ukuran: <span class="font-bold text-neutral-900">{{ $item->size_name }}</span> @endif
                                        </p>
                                        @if($item->bonus_item_snapshot)
                                            <span class="inline-flex items-center gap-1 text-[10px] text-neutral-700 bg-neutral-100 px-2 py-0.5 rounded-md mt-1 font-semibold">
                                                <i class="fa-solid fa-gift text-[9px] text-neutral-500"></i> Bonus: {{ $item->bonus_item_snapshot }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-left sm:text-right shrink-0">
                                    <span class="text-neutral-500 text-xs">{{ $item->quantity }} &times; Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                    <p class="font-extrabold text-neutral-950 text-sm mt-0.5">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer Kartu: Informasi Total, Ekspedisi, dan Aksi -->
                    <div class="p-4 sm:p-5 bg-neutral-50/50 border-t border-neutral-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 text-xs">
                        <div class="space-y-0.5 text-neutral-500">
                            <p>
                                Penerima: <strong class="text-neutral-900">{{ $order->recipient_first_name }} {{ $order->recipient_last_name }}</strong> 
                                ({{ $order->city }}, {{ $order->province }})
                            </p>
                            <p>
                                Kurir: <span class="text-neutral-900 font-medium">{{ $order->courier_name }}</span>
                                @if($order->seller)
                                    &bull; Mitra: <span class="text-neutral-900 font-medium">{{ $order->seller->name }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-bold text-neutral-400 block">Total Belanja</span>
                                <span class="text-base font-extrabold text-neutral-950">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('account.order.detail', $order->order_number) }}" 
                                   class="px-4 py-2.5 bg-neutral-950 hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-xs">
                                    Detail Pesanan
                                </a>
                                @if($order->payment_status !== 'verified')
                                    <a href="{{ route('checkout.success', $order->order_number) }}" 
                                       class="px-3.5 py-2.5 border border-neutral-300 hover:border-black text-neutral-800 text-xs font-bold uppercase tracking-wider rounded-xl transition" 
                                       title="Periksa atau unggah bukti transfer">
                                        Bukti Bayar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection
