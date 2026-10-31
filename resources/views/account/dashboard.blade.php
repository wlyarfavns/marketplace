@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">Dasbor Pelanggan</span>
            <h1 class="text-3xl font-serif font-bold text-gray-950 mt-1">Akun Saya</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 bg-black text-white text-xs font-semibold uppercase tracking-wider">
                Status: {{ $user->customer_type }}
            </span>
            <a href="{{ route('account.profile') }}" class="px-4 py-2 border border-gray-300 text-xs font-semibold hover:border-black transition">
                Edit Profil & Alamat
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12 space-y-10">

    <!-- 4 Kartu Statistik Belanja Sesuai PRD -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="p-6 border border-gray-200 bg-white space-y-2">
            <span class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Total Pesanan</span>
            <div class="text-3xl font-bold text-gray-950">{{ $totalOrders }}</div>
            <p class="text-xs text-gray-500">Seluruh transaksi Anda</p>
        </div>

        <div class="p-6 border border-gray-200 bg-white space-y-2">
            <span class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Pesanan Aktif</span>
            <div class="text-3xl font-bold text-amber-600">{{ $activeOrders }}</div>
            <p class="text-xs text-gray-500">Sedang diproses / dikirim</p>
        </div>

        <div class="p-6 border border-gray-200 bg-white space-y-2">
            <span class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Pesanan Selesai</span>
            <div class="text-3xl font-bold text-black">{{ $completedOrders }}</div>
            <p class="text-xs text-gray-500">Telah sampai di tujuan</p>
        </div>

        <div class="p-6 border border-gray-200 bg-white space-y-2">
            <span class="text-[11px] uppercase tracking-wider font-semibold text-gray-400">Total Belanja</span>
            <div class="text-2xl font-bold text-gray-950 truncate">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
            <p class="text-xs text-gray-500">Transaksi terverifikasi</p>
        </div>

    </div>

    <!-- Informasi Preferensi Cepat -->
    <div class="p-6 border border-gray-200 bg-gray-50 grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
        <div>
            <span class="text-gray-400 font-semibold uppercase block mb-1">Alamat Utama</span>
            <p class="font-medium text-gray-900">{{ $user->address ?? 'Belum diatur' }}</p>
            <p class="text-gray-500">{{ $user->city ?? '' }}, {{ $user->province ?? '' }}</p>
        </div>
        <div>
            <span class="text-gray-400 font-semibold uppercase block mb-1">Mitra Seller Langganan</span>
            <p class="font-bold text-gray-900">{{ $user->default_seller_code ?? 'Tidak ada' }}</p>
            <p class="text-gray-500 text-[11px]">Otomatis terisi saat checkout</p>
        </div>
        <div>
            <span class="text-gray-400 font-semibold uppercase block mb-1">Ekspedisi Favorit</span>
            <p class="font-bold text-gray-900">{{ $user->favorite_courier ?? 'JNE' }}</p>
            <p class="text-gray-500 text-[11px]">Pilihan utama pengiriman</p>
        </div>
    </div>

    <!-- 5 Pesanan Terakhir -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-serif font-bold text-gray-950">Pesanan Terbaru Anda</h2>
            <a href="{{ route('account.orders') }}" class="text-xs uppercase tracking-wider font-semibold underline text-black">
                Lihat Seluruh Riwayat &rarr;
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="p-8 border border-dashed border-gray-200 bg-white text-center text-xs text-gray-500">
                Belum ada transaksi pesanan yang dibuat.
            </div>
        @else
            <div class="border border-gray-200 bg-white divide-y divide-gray-100">
                @foreach($recentOrders as $ro)
                    <div class="p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs">
                        <div>
                            <span class="font-mono font-bold text-gray-900 block">{{ $ro->order_number }}</span>
                            <span class="text-gray-400 text-[11px]">{{ $ro->created_at->format('d F Y, H:i') }} &bull; {{ $ro->items->count() }} Item</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div>
                                <span class="text-[10px] uppercase font-semibold text-gray-400 block">Total</span>
                                <span class="font-bold text-gray-950">Rp {{ number_format($ro->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                @if($ro->order_status === 'completed')
                                    <span class="bg-blue-50 text-black px-2.5 py-1 border border-blue-200 font-semibold uppercase text-[10px]">Selesai</span>
                                @elseif($ro->order_status === 'processing')
                                    <span class="bg-blue-50 text-blue-700 px-2.5 py-1 border border-blue-200 font-semibold uppercase text-[10px]">Diproses</span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 px-2.5 py-1 border border-amber-200 font-semibold uppercase text-[10px]">Menunggu</span>
                                @endif
                            </div>
                            <a href="{{ route('account.order.detail', $ro->order_number) }}" class="border border-black text-black px-3.5 py-1.5 text-xs font-semibold hover:bg-black hover:text-white transition">
                                Rincian
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

