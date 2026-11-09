@extends('layouts.app')

@section('content')
<div class="border-b border-neutral-200 bg-neutral-50/80 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-neutral-500 uppercase tracking-widest font-semibold mb-1">
                <a href="{{ route('account.dashboard') }}" class="hover:text-black">Akun Saya</a>
                <span>/</span>
                <a href="{{ route('account.transactions') }}" class="hover:text-black">Riwayat Transaksi</a>
                <span>/</span>
                <span class="text-neutral-900 font-mono">{{ $transaction->transaction_number }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">Rincian Transaksi</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('account.transactions') }}" class="px-4 py-2 border border-neutral-300 hover:border-black text-neutral-700 hover:text-black text-xs font-bold uppercase tracking-wider rounded-xl transition">
                &larr; Kembali
            </a>
            @if($transaction->order)
                <a href="{{ route('account.order.detail', $transaction->order->order_number) }}" class="px-4 py-2 bg-neutral-950 hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-xs">
                    Buka Pesanan Terkait
                </a>
            @endif
        </div>
    </div>
</div>

@include('account.partials.nav')

<div class="max-w-4xl mx-auto px-4 sm:px-8 pb-16 space-y-8">

    <!-- Status Transaksi Pembayaran -->
    <div class="p-6 bg-white border border-neutral-200 rounded-2xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 text-xs shadow-xs">
        <div>
            <span class="text-neutral-400 uppercase font-bold text-[10px] tracking-wider block mb-1">Nomor Transaksi</span>
            <p class="font-mono font-extrabold text-neutral-950 text-sm">{{ $transaction->transaction_number }}</p>
            <span class="text-[11px] text-neutral-500">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</span>
        </div>
        <div>
            <span class="text-neutral-400 uppercase font-bold text-[10px] tracking-wider block mb-1">Status Verifikasi</span>
            <span class="inline-block px-3 py-1 rounded-md text-xs font-bold uppercase border {{ $transaction->status_badge }}">
                @if($transaction->verification_status === 'verified')
                    <i class="fa-solid fa-check mr-1 text-[9px]"></i> Terverifikasi
                @elseif($transaction->verification_status === 'rejected')
                    <i class="fa-solid fa-xmark mr-1 text-[9px]"></i> Ditolak
                @else
                    <i class="fa-solid fa-clock mr-1 text-[9px]"></i> Menunggu
                @endif
            </span>
        </div>
        <div>
            <span class="text-neutral-400 uppercase font-bold text-[10px] tracking-wider block mb-1">Tahap Pembayaran</span>
            <p class="font-extrabold text-neutral-950 text-sm">{{ $transaction->stage_label }}</p>
            <span class="text-[11px] text-neutral-500">Transfer Bank</span>
        </div>
        <div>
            <span class="text-neutral-400 uppercase font-bold text-[10px] tracking-wider block mb-1">Nominal Transfer</span>
            <p class="font-black text-neutral-950 text-base">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
            @if($transaction->verified_at)
                <span class="text-[10px] text-green-700 font-semibold">Tervalidasi: {{ $transaction->verified_at->format('d/m/Y') }}</span>
            @endif
        </div>
    </div>

    <!-- Informasi Rekening Tujuan & Bukti Transfer -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        <!-- Rekening Bank FOOTWORK -->
        <div class="bg-white border border-neutral-200 rounded-2xl p-6 space-y-3 shadow-xs">
            <h4 class="font-extrabold uppercase tracking-wider text-neutral-900 flex items-center gap-2 border-b border-neutral-100 pb-3">
                <i class="fa-solid fa-building-columns text-neutral-400"></i> Rekening Tujuan Transfer
            </h4>
            @if($transaction->bankAccount)
                <div class="space-y-2 pt-1">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-neutral-400 block">Bank</span>
                        <p class="font-extrabold text-neutral-950 text-sm">{{ $transaction->bankAccount->bank_name }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-neutral-400 block">Nomor Rekening</span>
                        <p class="font-mono font-extrabold text-neutral-900 text-sm">{{ $transaction->bankAccount->account_number }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-neutral-400 block">Atas Nama Rekening</span>
                        <p class="font-semibold text-neutral-800">{{ $transaction->bankAccount->account_name }}</p>
                    </div>
                </div>
            @else
                <p class="text-neutral-500 italic">Informasi rekening tidak tersedia.</p>
            @endif

            @if($transaction->notes)
                <div class="mt-4 pt-3 border-t border-neutral-100">
                    <span class="text-[10px] uppercase font-bold text-neutral-400 block mb-1">Catatan Anda</span>
                    <p class="p-3 bg-neutral-50 rounded-xl border border-neutral-200 text-neutral-700 italic">"{{ $transaction->notes }}"</p>
                </div>
            @endif
        </div>

        <!-- Bukti Gambar Transfer -->
        <div class="bg-white border border-neutral-200 rounded-2xl p-6 space-y-3 shadow-xs">
            <h4 class="font-extrabold uppercase tracking-wider text-neutral-900 flex items-center gap-2 border-b border-neutral-100 pb-3">
                <i class="fa-solid fa-image text-neutral-400"></i> Berkas Bukti Pembayaran
            </h4>
            
            <div class="pt-1">
                @if($transaction->proof_file_path)
                    <div class="rounded-xl border border-neutral-200 overflow-hidden bg-neutral-50 max-h-60 flex items-center justify-center p-2">
                        <img src="{{ asset('storage/' . $transaction->proof_file_path) }}" 
                             alt="Bukti Transfer {{ $transaction->transaction_number }}" 
                             class="max-h-56 max-w-full object-contain rounded-lg"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'py-12 text-center text-neutral-400 text-xs\'><i class=\'fa-solid fa-file-pdf text-3xl mb-2 block text-neutral-600\'></i><span>Berkas Dokumen Terlampir</span></div>';">
                    </div>
                    <div class="mt-3 flex justify-between items-center text-xs">
                        <span class="text-neutral-400 text-[11px]">Format: JPG/PNG/PDF</span>
                        <a href="{{ asset('storage/' . $transaction->proof_file_path) }}" target="_blank" class="font-bold text-neutral-950 underline hover:text-neutral-700 flex items-center gap-1">
                            <span>Lihat Ukuran Penuh</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                @else
                    <div class="py-12 text-center text-neutral-400 text-xs">
                        <i class="fa-regular fa-image text-2xl mb-1 block"></i>
                        <span>Berkas tidak ditemukan</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Informasi Pesanan Terkait -->
    @if($transaction->order)
        <div class="bg-white border border-neutral-200 rounded-2xl p-6 space-y-4 shadow-xs">
            <div class="flex justify-between items-center border-b border-neutral-100 pb-3 text-xs">
                <h4 class="font-extrabold uppercase tracking-wider text-neutral-900 flex items-center gap-2">
                    <i class="fa-solid fa-box-archive text-neutral-400"></i> Pesanan Terkait: 
                    <span class="font-mono font-bold text-neutral-950">{{ $transaction->order->order_number }}</span>
                </h4>
                <a href="{{ route('account.order.detail', $transaction->order->order_number) }}" class="font-bold text-neutral-950 underline hover:text-neutral-700">
                    Buka Rincian Lengkap &rarr;
                </a>
            </div>

            <!-- Ringkasan Produk dalam Pesanan -->
            <div class="divide-y divide-neutral-100 text-xs">
                @foreach($transaction->order->items as $item)
                    <div class="py-3 flex justify-between items-center gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-neutral-100 border border-neutral-200 overflow-hidden shrink-0 flex items-center justify-center">
                                @if($item->product && $item->product->primary_image_url)
                                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name_snapshot }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-shoe-prints text-[10px] text-neutral-300"></i>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-neutral-900">{{ $item->product_name_snapshot }}</p>
                                <span class="text-neutral-400 text-[11px]">{{ $item->quantity }} pcs &bull; {{ $item->size_name }}</span>
                            </div>
                        </div>
                        <span class="font-bold text-neutral-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Total Perbandingan -->
            <div class="pt-3 border-t border-neutral-100 flex flex-col sm:flex-row justify-between items-start sm:items-center text-xs text-neutral-600 gap-2">
                <div>
                    <span>Penerima: <strong class="text-neutral-900">{{ $transaction->order->recipient_first_name }} {{ $transaction->order->recipient_last_name }}</strong></span>
                    <span class="text-neutral-400 mx-1">&bull;</span>
                    <span>Status Pesanan: <strong class="text-neutral-900 uppercase">{{ $transaction->order->order_status_label }}</strong></span>
                </div>
                <div>
                    <span>Total Tagihan Pesanan: <strong class="text-neutral-950 text-sm font-extrabold">Rp {{ number_format($transaction->order->grand_total, 0, ',', '.') }}</strong></span>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

