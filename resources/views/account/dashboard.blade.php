@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="text-xs uppercase tracking-widest text-black font-bold">Dasbor Pelanggan</span>
            <h1 class="text-3xl font-serif font-black text-gray-950 mt-1">Akun Saya</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 bg-black text-white text-xs font-bold uppercase tracking-wider rounded border-2 border-black">
                Status: {{ $user->customer_type === 'umum' ? 'Pengunjung Umum' : ($user->customer_type === 'member' ? 'Member Resmi' : 'Mitra Distributor') }}
            </span>
            <a href="{{ route('account.profile') }}" class="px-4 py-2 border-2 border-black bg-white text-black text-xs font-bold hover:bg-black hover:text-white transition rounded shadow-xs active:translate-y-0.5 cursor-pointer">
                Edit Profil & Alamat
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12 space-y-10">

    <!-- 4 Kartu Statistik Belanja Sesuai PRD -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="p-6 border-2 border-gray-200 rounded-lg bg-white space-y-2 shadow-2xs">
            <span class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Total Pesanan</span>
            <div class="text-3xl font-black text-gray-950">{{ $totalOrders }}</div>
            <p class="text-xs text-gray-500 font-medium">Seluruh transaksi Anda</p>
        </div>

        <div class="p-6 border-2 border-gray-200 rounded-lg bg-white space-y-2 shadow-2xs">
            <span class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Pesanan Aktif</span>
            <div class="text-3xl font-black text-amber-600">{{ $activeOrders }}</div>
            <p class="text-xs text-gray-500 font-medium">Sedang diproses / dikirim</p>
        </div>

        <div class="p-6 border-2 border-gray-200 rounded-lg bg-white space-y-2 shadow-2xs">
            <span class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Pesanan Selesai</span>
            <div class="text-3xl font-black text-black">{{ $completedOrders }}</div>
            <p class="text-xs text-gray-500 font-medium">Telah sampai di tujuan</p>
        </div>

        <div class="p-6 border-2 border-gray-200 rounded-lg bg-white space-y-2 shadow-2xs">
            <span class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Total Belanja</span>
            <div class="text-2xl font-black text-gray-950 truncate">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
            <p class="text-xs text-gray-500 font-medium">Transaksi terverifikasi</p>
        </div>

    </div>

    <!-- Informasi Preferensi Cepat -->
    <div class="p-6 border-2 border-gray-200 rounded-lg bg-gray-50 grid grid-cols-1 md:grid-cols-3 gap-6 text-xs shadow-2xs">
        <div>
            <span class="text-gray-500 font-bold uppercase block mb-1">Alamat Utama</span>
            <p class="font-bold text-gray-900">{{ $user->address ?? 'Belum diatur' }}</p>
            <p class="text-gray-500">{{ $user->city ?? '' }}, {{ $user->province ?? '' }}</p>
        </div>
        <div>
            <span class="text-gray-500 font-bold uppercase block mb-1">Mitra Seller Langganan</span>
            <p class="font-bold text-gray-900">{{ $user->default_seller_code ?? 'Tidak ada' }}</p>
            <p class="text-gray-500 text-[11px]">Otomatis terisi saat checkout</p>
        </div>
        <div>
            <span class="text-gray-500 font-bold uppercase block mb-1">Ekspedisi Favorit</span>
            <p class="font-bold text-gray-900">{{ $user->favorite_courier ?? 'JNE' }}</p>
            <p class="text-gray-500 text-[11px]">Pilihan utama pengiriman</p>
        </div>
    </div>

    <!-- 5 Pesanan Terakhir -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-serif font-black text-gray-950">Pesanan Terbaru Anda</h2>
            <a href="{{ route('account.orders') }}" class="text-xs uppercase tracking-wider font-bold underline text-black hover:text-gray-700">
                Lihat Seluruh Riwayat &rarr;
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="p-8 border-2 border-dashed border-gray-200 bg-white rounded-lg text-center text-xs text-gray-500">
                Belum ada transaksi pesanan yang dibuat.
            </div>
        @else
            <div class="border-2 border-gray-200 rounded-lg bg-white divide-y divide-gray-100 overflow-hidden shadow-2xs">
                @foreach($recentOrders as $ro)
                    <div class="p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs">
                        <div>
                            <span class="font-mono font-bold text-gray-900 block">{{ $ro->order_number }}</span>
                            <span class="text-gray-400 text-[11px]">{{ $ro->created_at->format('d F Y, H:i') }} &bull; {{ $ro->items->count() }} Item Sepatu</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-gray-400 block">Total</span>
                                <span class="font-bold text-gray-950">Rp {{ number_format($ro->grand_total, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                @if($ro->order_status === 'completed')
                                    <span class="bg-gray-100 text-black px-2.5 py-1 border border-gray-300 font-bold uppercase text-[10px] rounded">Selesai</span>
                                @elseif($ro->order_status === 'processing')
                                    <span class="bg-blue-50 text-blue-700 px-2.5 py-1 border border-blue-200 font-bold uppercase text-[10px] rounded">Diproses</span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 px-2.5 py-1 border border-amber-200 font-bold uppercase text-[10px] rounded">Menunggu</span>
                                @endif
                            </div>
                            <a href="{{ route('account.order.detail', $ro->order_number) }}" class="border-2 border-black bg-white text-black px-4 py-1.5 text-xs font-bold hover:bg-black hover:text-white transition rounded shadow-xs active:translate-y-0.5 cursor-pointer">
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
