@extends('layouts.app')

@section('content')
<div class="border-b border-neutral-200 bg-neutral-50/80 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-neutral-500 uppercase tracking-widest font-semibold mb-1">
                <a href="{{ route('account.dashboard') }}" class="hover:text-black">Akun Saya</a>
                <span>/</span>
                <span class="text-neutral-900">Riwayat Transaksi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">Riwayat Transaksi Pembayaran</h1>
        </div>
        <a href="{{ route('account.orders') }}" class="px-4 py-2 bg-neutral-950 hover:bg-neutral-800 text-white text-xs uppercase font-bold tracking-wider transition rounded-lg shadow-xs">
            Lihat Pesanan Saya &rarr;
        </a>
    </div>
</div>

@include('account.partials.nav')

<div class="max-w-7xl mx-auto px-4 sm:px-8 pb-16 space-y-6">

    <!-- Filter & Pencarian Transaksi -->
    <div class="bg-white border border-neutral-200 rounded-2xl p-5 shadow-xs">
        <form action="{{ route('account.transactions') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Nomor Transaksi / Pesanan -->
                <div class="space-y-1 sm:col-span-2 lg:col-span-2">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">Cari Transaksi / Pesanan</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari TRX-... atau ORD..."
                               class="w-full text-xs px-3.5 py-2.5 pl-9 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                    </div>
                </div>

                <!-- Filter Status Transaksi -->
                <div class="space-y-1">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">Status Pembayaran</label>
                    <select name="status" class="w-full text-xs px-3 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950 bg-white">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi (Sukses)</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Filter Tahap Pembayaran -->
                <div class="space-y-1">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">Tahap Bayar</label>
                    <select name="stage" class="w-full text-xs px-3 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950 bg-white">
                        <option value="">Semua Tahap</option>
                        <option value="full" {{ request('stage') === 'full' ? 'selected' : '' }}>Bayar Penuh (100%)</option>
                        <option value="dp" {{ request('stage') === 'dp' ? 'selected' : '' }}>Uang Muka (DP)</option>
                        <option value="remaining" {{ request('stage') === 'remaining' ? 'selected' : '' }}>Pelunasan Sisa</option>
                    </select>
                </div>

                <!-- Sort Urutan -->
                <div class="space-y-1">
                    <label class="text-[11px] font-bold uppercase tracking-wider text-neutral-500">Urutkan</label>
                    <select name="sort" class="w-full text-xs px-3 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950 bg-white">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort', 'oldest') === 'oldest' ? 'selected' : '' }}>Terlama</option>
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
                    @if(request()->hasAny(['search', 'status', 'stage', 'date_from', 'date_to', 'sort']))
                        <a href="{{ route('account.transactions') }}" class="px-3.5 py-2 text-neutral-600 hover:text-black text-xs font-semibold">
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

    <!-- Daftar Kartu Transaksi -->
    @if($transactions->isEmpty())
        <div class="text-center py-20 border border-dashed border-neutral-200 bg-white rounded-2xl p-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-neutral-100 flex items-center justify-center text-neutral-400">
                <i class="fa-solid fa-receipt text-2xl"></i>
            </div>
            <h3 class="text-base font-bold text-neutral-900">Tidak Ada Transaksi Pembayaran</h3>
            <p class="text-xs text-neutral-500 mt-1 max-w-md mx-auto">
                @if(request()->hasAny(['search', 'status', 'stage', 'date_from', 'date_to']))
                    Tidak ada catatan transaksi yang sesuai dengan filter pencarian Anda. Silakan ubah atau reset filter.
                @else
                    Belum ada bukti pembayaran transfer yang dikonfirmasikan. Setiap kali Anda mengirim bukti bayar pesanan, riwayat transaksi akan tercatat di sini.
                @endif
            </p>
            <div class="mt-5">
                @if(request()->hasAny(['search', 'status', 'stage', 'date_from', 'date_to']))
                    <a href="{{ route('account.transactions') }}" class="inline-block bg-neutral-900 text-white px-5 py-2.5 text-xs uppercase tracking-wider font-bold rounded-xl hover:bg-neutral-800 transition">
                        Reset Filter
                    </a>
                @else
                    <a href="{{ route('account.orders') }}" class="inline-block bg-neutral-950 text-white px-6 py-2.5 text-xs uppercase tracking-widest font-bold rounded-xl hover:bg-neutral-800 transition shadow-xs">
                        Lihat Pesanan Belum Lunas
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($transactions as $t)
                <div class="bg-white border border-neutral-200 rounded-2xl overflow-hidden shadow-xs hover:border-neutral-300 transition">
                    <!-- Header Transaksi -->
                    <div class="p-4 sm:p-5 bg-neutral-50/70 border-b border-neutral-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="font-mono font-extrabold text-neutral-950 text-sm tracking-tight">{{ $t->transaction_number }}</span>
                            <span class="text-neutral-300">|</span>
                            <span class="text-neutral-500 text-[11px]">{{ $t->created_at->format('d M Y, H:i') }} WIB</span>
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md bg-neutral-200/80 text-neutral-800">
                                {{ $t->stage_label }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded-md border {{ $t->status_badge }}">
                                @if($t->verification_status === 'verified')
                                    <i class="fa-solid fa-check mr-1 text-[9px]"></i> Terverifikasi
                                @elseif($t->verification_status === 'rejected')
                                    <i class="fa-solid fa-xmark mr-1 text-[9px]"></i> Ditolak
                                @else
                                    <i class="fa-solid fa-clock mr-1 text-[9px]"></i> Menunggu Verifikasi
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Body Transaksi -->
                    <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-5 text-xs items-center">
                        <!-- Kolom Pesanan Terkait -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-bold text-neutral-400 block tracking-wider">Pesanan Terkait</span>
                            @if($t->order)
                                <a href="{{ route('account.order.detail', $t->order->order_number) }}" class="font-mono font-bold text-neutral-950 text-sm hover:underline flex items-center gap-1.5">
                                    <span>{{ $t->order->order_number }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-neutral-400"></i>
                                </a>
                                <p class="text-neutral-500 text-[11px]">
                                    Penerima: {{ $t->order->recipient_first_name }} {{ $t->order->recipient_last_name }}
                                </p>
                            @else
                                <span class="text-neutral-400 italic">Pesanan tidak ditemukan</span>
                            @endif
                        </div>

                        <!-- Kolom Rekening Tujuan -->
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-bold text-neutral-400 block tracking-wider">Tujuan Transfer Bank</span>
                            @if($t->bankAccount)
                                <p class="font-bold text-neutral-900 text-sm">{{ $t->bankAccount->bank_name }}</p>
                                <p class="text-neutral-500 text-[11px]">No. Rek: <span class="font-mono text-neutral-800">{{ $t->bankAccount->account_number }}</span> a/n {{ $t->bankAccount->account_name }}</p>
                            @else
                                <p class="font-medium text-neutral-800">Transfer Bank Manual</p>
                            @endif
                        </div>

                        <!-- Kolom Nominal Pembayaran -->
                        <div class="text-left md:text-right space-y-1">
                            <span class="text-[10px] uppercase font-bold text-neutral-400 block tracking-wider">Nominal Pembayaran</span>
                            <p class="text-lg font-black text-neutral-950">Rp {{ number_format($t->amount, 0, ',', '.') }}</p>
                            @if($t->verified_at)
                                <p class="text-[11px] text-green-700 font-semibold">Diverifikasi: {{ $t->verified_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Kartu: Bukti & Tombol Aksi -->
                    <div class="p-4 sm:p-5 bg-neutral-50/50 border-t border-neutral-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs">
                        <div class="flex items-center gap-2 text-neutral-500">
                            <i class="fa-solid fa-file-invoice text-neutral-400"></i>
                            @if($t->notes)
                                <span>Catatan: <em>"{{ Str::limit($t->notes, 60) }}"</em></span>
                            @else
                                <span>Bukti transfer tersimpan pada sistem</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                            @if($t->order)
                                <a href="{{ route('account.order.detail', $t->order->order_number) }}" class="px-3 py-2 border border-neutral-300 hover:border-black text-neutral-700 text-xs font-bold uppercase rounded-xl transition">
                                    Lihat Pesanan
                                </a>
                            @endif
                            <a href="{{ route('account.transaction.detail', $t->id) }}" class="px-4 py-2 bg-neutral-950 hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-xs">
                                Rincian Transaksi
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $transactions->links() }}
        </div>
    @endif

</div>
@endsection

