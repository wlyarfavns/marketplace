@extends('layouts.admin')

@section('page_title', '1. Dashboard Analitik & Statistik Down Payment (DP)')

@section('content')
<div class="space-y-8">
    
    <!-- 1. KPI Utama (4 Kartu) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-6 bg-white border border-slate-200 rounded-none space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pesanan</span>
            <div class="text-3xl font-bold text-slate-900">{{ number_format($totalOrders) }}</div>
            <p class="text-xs text-slate-500">Semua pesanan tercatat</p>
        </div>

        <div class="p-6 bg-white border border-slate-200 rounded-none space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pendapatan Terverifikasi</span>
            <div class="text-2xl font-bold text-emerald-700 truncate">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-500">Pembayaran terverifikasi</p>
        </div>

        <div class="p-6 bg-white border border-slate-200 rounded-none space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pelanggan Terdaftar</span>
            <div class="text-3xl font-bold text-slate-900">{{ number_format($totalCustomers) }}</div>
            <p class="text-xs text-slate-500">Member, Distributor & Umum</p>
        </div>

        <div class="p-6 bg-white border border-slate-200 rounded-none space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Produk Aktif di Katalog</span>
            <div class="text-3xl font-bold text-slate-900">{{ number_format($activeProducts) }}</div>
            <p class="text-xs text-slate-500">Ready Stock & Pre-Order</p>
        </div>
    </div>

    <!-- 2. Status Operasional Pesanan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="p-5 bg-amber-50 border border-amber-200 flex justify-between items-center">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Pesanan Pending</span>
                <p class="text-xs text-amber-600 mt-0.5">Menunggu konfirmasi / transfer</p>
            </div>
            <span class="text-3xl font-bold text-amber-800">{{ $pendingOrders }}</span>
        </div>

        <div class="p-5 bg-blue-50 border border-blue-200 flex justify-between items-center">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Sedang Diproses</span>
                <p class="text-xs text-blue-600 mt-0.5">Siap packing & ekspedisi</p>
            </div>
            <span class="text-3xl font-bold text-blue-800">{{ $processingOrders }}</span>
        </div>

        <div class="p-5 bg-emerald-50 border border-emerald-200 flex justify-between items-center">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Pesanan Selesai</span>
                <p class="text-xs text-emerald-600 mt-0.5">Barang diterima pelanggan</p>
            </div>
            <span class="text-3xl font-bold text-emerald-800">{{ $completedOrders }}</span>
        </div>
    </div>

    <!-- 3. Kartu Khusus Statistik Down Payment (DP) Sesuai PRD Poin 4.2.1 -->
    <div class="bg-white border border-slate-200 p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-coins text-slate-700"></i>
                    Statistik Skema Down Payment (Uang Muka / DP)
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Pantauan transaksi DP Pre-Order dan pelunasan piutang mitra.</p>
            </div>
            <span class="text-[10px] uppercase font-bold px-2 py-0.5 bg-slate-100 text-slate-700">Real-Time</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-xs">
            <div class="p-4 bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Pesanan DP Masuk</span>
                <p class="text-xl font-bold text-slate-900">{{ $totalDpOrders }}</p>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">DP Terbayar</span>
                <p class="text-xl font-bold text-blue-700">{{ $dpPaidOrders }}</p>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">DP Pending</span>
                <p class="text-xl font-bold text-amber-700">{{ $dpPendingOrders }}</p>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Lunas Penuh</span>
                <p class="text-xl font-bold text-emerald-700">{{ $fullyPaidOrders }}</p>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 space-y-1 md:col-span-1">
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Omzet Masuk (DP)</span>
                <p class="text-sm font-bold text-slate-900 truncate">Rp {{ number_format($revenueDp, 0, ',', '.') }}</p>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 space-y-1 md:col-span-1">
                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Piutang Sisa Tagihan</span>
                <p class="text-sm font-bold text-slate-900 truncate">Rp {{ number_format($remainingReceivable, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- 4. Dua Kolom: Pesanan Terbaru & Produk Terpopuler -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Pesanan Terbaru (7 Cols) -->
        <div class="lg:col-span-7 bg-white border border-slate-200 p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">5 Pesanan Terbaru</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-slate-600 hover:text-black">
                    Kelola Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($recentOrders as $ro)
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <p class="font-mono font-bold text-slate-900">{{ $ro->order_number }}</p>
                            <p class="text-slate-500 text-[11px]">
                                {{ $ro->recipient_first_name }} &bull; {{ $ro->city }} &bull; 
                                <span class="uppercase font-semibold">{{ $ro->payment_method_type }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-slate-900 block">Rp {{ number_format($ro->grand_total, 0, ',', '.') }}</span>
                            <span class="text-[10px] uppercase font-semibold px-2 py-0.5 rounded
                                {{ $ro->order_status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($ro->order_status === 'processing' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700') }}">
                                {{ $ro->order_status }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Produk Terpopuler & Kupon Aktif (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Produk Terpopuler -->
            <div class="bg-white border border-slate-200 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">Produk Terpopuler</h3>
                    <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-black">Katalog</a>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($popularProducts as $pop)
                        <div class="py-2.5 flex justify-between items-center">
                            <div class="truncate w-48">
                                <p class="font-medium text-slate-900 truncate">{{ $pop->name }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $pop->sku }}</span>
                            </div>
                            <span class="font-bold text-slate-700">{{ $pop->order_items_count }} Terjual</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kupon Aktif -->
            <div class="bg-white border border-slate-200 p-6 space-y-3">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">Kupon Pemasaran Aktif</h3>
                    <a href="{{ route('admin.coupons.index') }}" class="text-xs font-semibold text-slate-600 hover:text-black">Kelola</a>
                </div>
                <div class="space-y-2 text-xs">
                    @foreach($activeCoupons as $coup)
                        <div class="p-2.5 bg-slate-50 border border-slate-200 flex justify-between items-center">
                            <div>
                                <span class="font-mono font-bold text-slate-900">{{ $coup->code }}</span>
                                <span class="text-[10px] text-slate-500 block">Target: {{ $coup->target_audience }}</span>
                            </div>
                            <span class="font-bold text-slate-800">
                                {{ $coup->discount_type === 'percentage' ? $coup->discount_value . '%' : 'Rp ' . number_format($coup->discount_value, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

    <!-- Tombol Aksi Cepat Sesuai PRD Poin 4.2.1 -->
    <div class="bg-slate-900 text-white p-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div>
            <h4 class="text-sm font-bold uppercase tracking-wider">Aksi Cepat Administrator</h4>
            <p class="text-xs text-slate-400 mt-0.5">Akses modul utama dengan satu klik.</p>
        </div>
        <div class="flex flex-wrap gap-3 text-xs">
            <a href="{{ route('admin.products.create') }}" class="bg-white text-slate-900 font-bold px-4 py-2 hover:bg-slate-100 transition">
                + Tambah Produk Baru
            </a>
            <a href="{{ route('admin.orders.index') }}" class="border border-slate-700 px-4 py-2 hover:bg-slate-800 transition">
                Lihat Pesanan
            </a>
            <a href="{{ route('admin.sellers.report') }}" class="border border-slate-700 px-4 py-2 hover:bg-slate-800 transition">
                Laporan Mitra
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="border border-slate-700 px-4 py-2 hover:bg-slate-800 transition">
                Buat Kupon
            </a>
        </div>
    </div>

</div>
@endsection

