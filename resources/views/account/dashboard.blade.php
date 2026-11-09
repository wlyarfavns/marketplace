@extends('layouts.app')

@section('content')
<div class="border-b border-neutral-200 bg-neutral-50/80 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="text-xs uppercase tracking-widest text-neutral-500 font-semibold">Dasbor Pelanggan</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight mt-0.5">Akun Saya</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-neutral-950 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow-2xs">
                Peran: {{ $user->role === 'admin' ? 'Administrator' : 'Pelanggan' }}
            </span>
            <a href="{{ route('account.profile') }}" class="px-4 py-2 bg-white border border-neutral-200 hover:border-black text-xs font-bold uppercase rounded-lg transition shadow-2xs">
                Edit Profil & Alamat
            </a>
        </div>
    </div>
</div>

@include('account.partials.nav')

<div class="max-w-7xl mx-auto px-4 sm:px-8 pb-16 space-y-10">

    <!-- 5 Kartu Statistik Belanja & Transaksi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <div class="p-5 border border-neutral-200 bg-white rounded-2xl space-y-1.5 shadow-xs">
            <span class="text-[10px] uppercase tracking-wider font-bold text-neutral-400">Total Pesanan</span>
            <div class="text-2xl font-black text-neutral-950">{{ $totalOrders }}</div>
            <p class="text-[11px] text-neutral-500">Seluruh pesanan Anda</p>
        </div>

        <div class="p-5 border border-neutral-200 bg-white rounded-2xl space-y-1.5 shadow-xs">
            <span class="text-[10px] uppercase tracking-wider font-bold text-neutral-400">Pesanan Aktif</span>
            <div class="text-2xl font-black text-amber-600">{{ $activeOrders }}</div>
            <p class="text-[11px] text-neutral-500">Sedang diproses / dikirim</p>
        </div>

        <div class="p-5 border border-neutral-200 bg-white rounded-2xl space-y-1.5 shadow-xs">
            <span class="text-[10px] uppercase tracking-wider font-bold text-neutral-400">Pesanan Selesai</span>
            <div class="text-2xl font-black text-neutral-950">{{ $completedOrders }}</div>
            <p class="text-[11px] text-neutral-500">Telah sampai di tujuan</p>
        </div>

        <div class="p-5 border border-neutral-200 bg-white rounded-2xl space-y-1.5 shadow-xs">
            <span class="text-[10px] uppercase tracking-wider font-bold text-neutral-400">Total Transaksi</span>
            <div class="text-2xl font-black text-blue-600">{{ $totalTransactions }}</div>
            <p class="text-[11px] text-neutral-500">Bukti transfer diajukan</p>
        </div>

        <div class="p-5 border border-neutral-200 bg-white rounded-2xl space-y-1.5 shadow-xs sm:col-span-2 lg:col-span-1">
            <span class="text-[10px] uppercase tracking-wider font-bold text-neutral-400">Total Belanja</span>
            <div class="text-xl font-black text-neutral-950 truncate">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
            <p class="text-[11px] text-neutral-500">Transaksi terverifikasi</p>
        </div>

    </div>

    <!-- Informasi Preferensi Cepat -->
    <div class="p-6 border border-neutral-200 bg-neutral-50/60 rounded-2xl grid grid-cols-1 md:grid-cols-3 gap-6 text-xs shadow-xs">
        <div>
            <span class="text-neutral-400 font-bold uppercase text-[10px] tracking-wider block mb-1">Alamat Utama</span>
            <p class="font-bold text-neutral-900">{{ $user->address ?? 'Belum diatur' }}</p>
            <p class="text-neutral-500 text-[11px]">{{ $user->city ?? '' }}{{ $user->city && $user->province ? ', ' : '' }}{{ $user->province ?? '' }}</p>
        </div>
        <div>
            <span class="text-neutral-400 font-bold uppercase text-[10px] tracking-wider block mb-1">Mitra Seller Langganan</span>
            <p class="font-bold text-neutral-900">{{ $user->default_seller_code ?? 'Toko Mandiri' }}</p>
            <p class="text-neutral-500 text-[11px]">Otomatis terisi saat checkout</p>
        </div>
        <div>
            <span class="text-neutral-400 font-bold uppercase text-[10px] tracking-wider block mb-1">Ekspedisi Favorit</span>
            <p class="font-bold text-neutral-900">{{ $user->favorite_courier ?? 'JNE' }}</p>
            <p class="text-neutral-500 text-[11px]">Pilihan utama pengiriman</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- 5 Pesanan Terakhir -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-base font-extrabold text-neutral-950 flex items-center gap-2">
                    <i class="fa-solid fa-box text-neutral-400"></i> Pesanan Terbaru
                </h2>
                <a href="{{ route('account.orders') }}" class="text-xs uppercase tracking-wider font-bold text-neutral-950 hover:underline">
                    Semua Pesanan &rarr;
                </a>
            </div>

            @if($recentOrders->isEmpty())
                <div class="p-8 border border-dashed border-neutral-200 bg-white rounded-2xl text-center text-xs text-neutral-500 shadow-xs">
                    Belum ada riwayat pesanan yang dibuat.
                </div>
            @else
                <div class="border border-neutral-200 bg-white rounded-2xl divide-y divide-neutral-100 shadow-xs overflow-hidden">
                    @foreach($recentOrders as $ro)
                        <div class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs">
                            <div class="space-y-0.5">
                                <a href="{{ route('account.order.detail', $ro->order_number) }}" class="font-mono font-bold text-neutral-950 hover:underline block">
                                    {{ $ro->order_number }}
                                </a>
                                <span class="text-neutral-400 text-[11px]">{{ $ro->created_at->format('d M Y, H:i') }} &bull; {{ $ro->items->count() }} Produk</span>
                            </div>
                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="text-left sm:text-right">
                                    <span class="font-extrabold text-neutral-950 block">Rp {{ number_format($ro->grand_total, 0, ',', '.') }}</span>
                                    <span class="text-[10px] font-bold uppercase rounded px-1.5 py-0.5 border {{ $ro->status_badge }}">
                                        {{ $ro->order_status_label }}
                                    </span>
                                </div>
                                <a href="{{ route('account.order.detail', $ro->order_number) }}" class="px-3 py-1.5 bg-neutral-950 hover:bg-neutral-800 text-white text-[11px] font-bold rounded-lg transition shadow-2xs">
                                    Rincian
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 5 Transaksi Pembayaran Terakhir -->
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-base font-extrabold text-neutral-950 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-neutral-400"></i> Transaksi Pembayaran Terbaru
                </h2>
                <a href="{{ route('account.transactions') }}" class="text-xs uppercase tracking-wider font-bold text-neutral-950 hover:underline">
                    Semua Transaksi &rarr;
                </a>
            </div>

            @if($recentTransactions->isEmpty())
                <div class="p-8 border border-dashed border-neutral-200 bg-white rounded-2xl text-center text-xs text-neutral-500 shadow-xs">
                    Belum ada bukti pembayaran yang dikonfirmasi.
                </div>
            @else
                <div class="border border-neutral-200 bg-white rounded-2xl divide-y divide-neutral-100 shadow-xs overflow-hidden">
                    @foreach($recentTransactions as $rt)
                        <div class="p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs">
                            <div class="space-y-0.5">
                                <a href="{{ route('account.transaction.detail', $rt->id) }}" class="font-mono font-bold text-neutral-950 hover:underline block">
                                    {{ $rt->transaction_number }}
                                </a>
                                <span class="text-neutral-400 text-[11px]">
                                    {{ $rt->created_at->format('d M Y, H:i') }} &bull; {{ $rt->stage_label }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="text-left sm:text-right">
                                    <span class="font-extrabold text-neutral-950 block">Rp {{ number_format($rt->amount, 0, ',', '.') }}</span>
                                    <span class="text-[10px] font-bold uppercase rounded px-1.5 py-0.5 border {{ $rt->status_badge }}">
                                        {{ $rt->verification_status }}
                                    </span>
                                </div>
                                <a href="{{ route('account.transaction.detail', $rt->id) }}" class="px-3 py-1.5 border border-neutral-300 hover:border-black text-neutral-800 text-[11px] font-bold rounded-lg transition shadow-2xs">
                                    Rincian
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
