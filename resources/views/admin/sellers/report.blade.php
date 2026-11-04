@extends('layouts.admin')

@section('page_title', '3. Laporan Penjualan & Performa Mitra (Closing Real-Time)')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Performa Closing Mitra Penjual (Sellers)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rekap real-time penjualan per ID Seller dan Brand untuk evaluasi komisi & reward.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.sellers.report.export', request()->query()) }}" class="bg-emerald-700 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-emerald-800 transition flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Export Laporan Mitra (Excel)
            </a>
            <a href="{{ route('admin.sellers.index') }}" class="border border-slate-300 text-slate-700 text-xs uppercase font-semibold px-4 py-2.5 hover:bg-slate-50 transition">
                Kelola Mitra
            </a>
        </div>
    </div>

    <!-- 3 Kartu Ringkasan Akumulasi Sesuai PRD Poin 4.2.7 -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="p-5 bg-white border border-slate-200 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400">Total Closing Hari Ini</span>
            <div class="text-3xl font-bold text-slate-900">{{ $totalClosingToday }} Transaksi</div>
            <p class="text-xs text-slate-500">Seluruh mitra seller aktif</p>
        </div>

        <div class="p-5 bg-white border border-slate-200 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400">Total Penjualan Bulan Ini</span>
            <div class="text-2xl font-bold text-emerald-700 truncate">Rp {{ number_format($totalSalesMonth, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-500">Bulan berjalan (Terverifikasi)</p>
        </div>

        <div class="p-5 bg-white border border-slate-200 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400">Total Penjualan Bulan Lalu</span>
            <div class="text-2xl font-bold text-slate-800 truncate">Rp {{ number_format($totalSalesLastMonth, 0, ',', '.') }}</div>
            <p class="text-xs text-slate-500">Bulan sebelumnya</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200 p-4">
        <form action="{{ route('admin.sellers.report') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <select name="brand_id" class="w-full px-3 py-2 border border-slate-300 bg-white">
                    <option value="">Semua Brand</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="seller_id" class="w-full px-3 py-2 border border-slate-300 bg-white">
                    <option value="">Semua Mitra Penjual</option>
                    @foreach($allSellers as $s)
                        <option value="{{ $s->id }}" {{ request('seller_id') == $s->id ? 'selected' : '' }}>{{ $s->seller_code }} — {{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full bg-slate-900 text-white font-semibold py-2">Filter Laporan</button>
                @if(request()->anyFilled(['brand_id', 'seller_id']))
                    <a href="{{ route('admin.sellers.report') }}" class="px-3 py-2 border border-slate-300 text-slate-500">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Laporan Performa Sesuai PRD Poin 4.2.7 -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">ID Seller</th>
                    <th class="p-4">Nama Mitra</th>
                    <th class="p-4">Brand Terkait</th>
                    <th class="p-4 text-center">Closing Hari Ini</th>
                    <th class="p-4 text-right">Penjualan Bulan Ini</th>
                    <th class="p-4 text-right">Penjualan Bulan Lalu</th>
                    <th class="p-4 text-center">Total Order</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($reportData as $row)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4">
                            <span class="font-mono font-bold text-xs bg-slate-100 px-2 py-1 border border-slate-200 text-slate-900">
                                {{ $row['seller']->seller_code }}
                            </span>
                        </td>
                        <td class="p-4 font-bold text-slate-900">
                            {{ $row['seller']->name }}
                            <span class="text-[10px] text-slate-400 block font-normal">{{ $row['seller']->phone }}</span>
                        </td>
                        <td class="p-4 font-semibold text-slate-700">
                            {{ $row['seller']->brand ? $row['seller']->brand->name : '-' }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="font-bold text-sm {{ $row['closing_today'] > 0 ? 'text-emerald-700' : 'text-slate-400' }}">
                                {{ $row['closing_today'] }} Order
                            </span>
                        </td>
                        <td class="p-4 text-right font-bold text-slate-900">
                            Rp {{ number_format($row['sales_month'], 0, ',', '.') }}
                        </td>
                        <td class="p-4 text-right font-medium text-slate-600">
                            Rp {{ number_format($row['sales_last_month'], 0, ',', '.') }}
                        </td>
                        <td class="p-4 text-center font-bold text-slate-700">
                            {{ $row['total_orders'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Tidak ada data performa mitra untuk filter yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $paginatedSellers->links() }}
    </div>

</div>
@endsection

