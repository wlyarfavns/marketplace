@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex justify-between items-center">
        <div>
            <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">Riwayat Belanja</span>
            <h1 class="text-3xl font-serif font-bold text-gray-950 mt-1">Daftar Pesanan Saya</h1>
        </div>
        <a href="{{ route('account.dashboard') }}" class="text-xs uppercase font-semibold text-gray-600 hover:text-black">
            &larr; Kembali ke Dasbor
        </a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12">
    @if($orders->isEmpty())
        <div class="text-center py-20 border border-dashed border-gray-200 bg-white">
            <i class="fa-solid fa-receipt text-3xl text-gray-300 mb-3 block"></i>
            <h3 class="text-base font-serif font-bold text-gray-900">Belum Ada Riwayat Pesanan</h3>
            <p class="text-xs text-gray-500 mt-1">Pesanan Anda akan tampil di halaman ini setelah proses checkout selesai.</p>
            <a href="{{ route('catalog') }}" class="inline-block mt-4 bg-black text-white px-6 py-2.5 text-xs uppercase tracking-widest font-semibold hover:bg-gray-800 transition">Mulai Belanja</a>
        </div>
    @else
        <div class="border border-gray-200 bg-white divide-y divide-gray-100">
            @foreach($orders as $order)
                <div class="p-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-sm font-bold text-gray-950">{{ $order->order_number }}</span>
                            <span class="text-gray-300">|</span>
                            <span class="text-xs text-gray-500">{{ $order->created_at->format('d F Y, H:i') }}</span>
                            <span class="text-xs font-semibold uppercase px-2 py-0.5 bg-gray-100 text-gray-800">
                                Skema: {{ strtoupper($order->payment_method_type) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            Penerima: <span class="font-medium text-gray-900">{{ $order->recipient_first_name }} {{ $order->recipient_last_name }}</span> &bull; 
                            Kota: {{ $order->city }} &bull; Ekspedisi: {{ $order->courier_name }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-6 text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-semibold text-gray-400 block">Status Pesanan</span>
                            @if($order->order_status === 'completed')
                                <span class="text-black font-bold uppercase">Selesai</span>
                            @elseif($order->order_status === 'processing')
                                <span class="text-gray-900 font-bold uppercase">Diproses</span>
                            @elseif($order->order_status === 'shipped')
                                <span class="text-black font-bold uppercase">Dikirim</span>
                            @else
                                <span class="text-gray-700 font-bold uppercase">Menunggu</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-[10px] uppercase font-semibold text-gray-400 block">Status Bayar</span>
                            @if($order->payment_status === 'verified')
                                <span class="text-black font-bold uppercase">Terverifikasi</span>
                            @elseif($order->payment_status === 'waiting_verification')
                                <span class="text-gray-900 font-bold uppercase">Sedang Diverifikasi</span>
                            @else
                                <span class="text-gray-700 font-bold uppercase">Belum Lunas</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-[10px] uppercase font-semibold text-gray-400 block">Grand Total</span>
                            <span class="text-sm font-bold text-gray-950">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('account.order.detail', $order->order_number) }}" class="px-4 py-2 bg-black text-white text-xs font-semibold uppercase tracking-wider hover:bg-black transition">
                                Detail Pesanan
                            </a>
                            <a href="{{ route('checkout.success', $order->order_number) }}" class="px-4 py-2 border border-gray-300 text-xs font-semibold uppercase tracking-wider hover:border-black transition" title="Unggah / Cek Bukti Transfer">
                                Bukti Transfer
                            </a>
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

