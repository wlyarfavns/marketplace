@extends('layouts.admin')

@section('page_title', '2. Kelola Pesanan & Faktur PDF')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Manajemen Transaksi & Pesanan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Filter transaksi, verifikasi bukti transfer, cetak faktur PDF, dan export data.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.export-excel') }}" class="bg-emerald-700 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-emerald-800 transition flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Export Laporan (Excel)
            </a>
        </div>
    </div>

    <!-- Filter Multi-Dimensi Sesuai PRD Poin 4.2.6 -->
    <div class="bg-white border border-slate-200 p-4">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-xs">
            <div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Order ID / Nama / WA..." 
                       class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
            </div>

            <div>
                <select name="order_status" class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                    <option value="">Semua Status Pesanan</option>
                    <option value="pending" {{ request('order_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('order_status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="shipped" {{ request('order_status') === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                    <option value="completed" {{ request('order_status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('order_status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div>
                <select name="payment_status" class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                    <option value="">Semua Status Bayar</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="waiting_verification" {{ request('payment_status') === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="verified" {{ request('payment_status') === 'verified' ? 'selected' : '' }}>Terverifikasi (Lunas/DP)</option>
                    <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div>
                <select name="payment_method_type" class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                    <option value="">Semua Skema Bayar</option>
                    <option value="full" {{ request('payment_method_type') === 'full' ? 'selected' : '' }}>Pembayaran Penuh (100%)</option>
                    <option value="dp" {{ request('payment_method_type') === 'dp' ? 'selected' : '' }}>Down Payment (DP)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full bg-slate-900 text-white font-semibold py-2 hover:bg-slate-800 transition">
                    Filter
                </button>
                @if(request()->anyFilled(['q', 'order_status', 'payment_status', 'payment_method_type']))
                    <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 border border-slate-300 text-slate-500 hover:text-black">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pesanan -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">No. Pesanan / Tanggal</th>
                    <th class="p-4">Penerima & WhatsApp</th>
                    <th class="p-4">Skema Bayar</th>
                    <th class="p-4 text-right">Grand Total</th>
                    <th class="p-4">Status Pesanan</th>
                    <th class="p-4">Status Pembayaran</th>
                    <th class="p-4 text-center">Faktur PDF</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $o)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4">
                            <span class="font-mono text-xs font-bold text-slate-900 block">{{ $o->order_number }}</span>
                            <span class="text-[10px] text-slate-400">{{ $o->created_at->format('d/m/Y H:i') }}</span>
                            @if($o->seller)
                                <span class="inline-block mt-1 text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.2 font-semibold">
                                    Mitra: {{ $o->seller->seller_code }}
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <p class="font-bold text-slate-800">{{ $o->recipient_first_name }} {{ $o->recipient_last_name }}</p>
                            <p class="text-slate-500 text-[11px] font-mono">{{ $o->whatsapp }}</p>
                            <span class="text-[10px] text-slate-400">{{ $o->city }} ({{ $o->courier_name }})</span>
                        </td>
                        <td class="p-4">
                            @if($o->payment_method_type === 'dp')
                                <span class="bg-slate-900 text-white font-bold px-1.5 py-0.5 text-[10px] uppercase">DP</span>
                                <span class="text-[11px] text-slate-600 block mt-0.5">DP: Rp {{ number_format($o->dp_amount, 0, ',', '.') }}</span>
                            @else
                                <span class="bg-slate-100 text-slate-800 font-bold px-1.5 py-0.5 text-[10px] uppercase">FULL (100%)</span>
                            @endif
                        </td>
                        <td class="p-4 text-right font-bold text-slate-900 text-sm">
                            Rp {{ number_format($o->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="p-4">
                            <form action="{{ route('admin.orders.update-status', $o->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="payment_status" value="{{ $o->payment_status }}">
                                <input type="hidden" name="dp_status" value="{{ $o->dp_status }}">
                                <select name="order_status" onchange="this.form.submit()" 
                                        class="text-xs px-2 py-1 border border-slate-300 font-semibold uppercase
                                        {{ $o->order_status === 'completed' ? 'bg-emerald-50 text-emerald-800' : ($o->order_status === 'processing' ? 'bg-blue-50 text-blue-800' : 'bg-amber-50 text-amber-800') }}">
                                    <option value="pending" {{ $o->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $o->order_status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                    <option value="shipped" {{ $o->order_status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="completed" {{ $o->order_status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $o->order_status === 'cancelled' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </form>
                        </td>
                        <td class="p-4">
                            @if($o->payment_status === 'verified')
                                <span class="text-emerald-700 font-bold text-[11px] flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> Terverifikasi
                                </span>
                            @elseif($o->payment_status === 'waiting_verification')
                                <span class="text-blue-700 font-bold text-[11px] flex items-center gap-1 animate-pulse">
                                    <i class="fa-solid fa-hourglass-half"></i> Perlu Verifikasi
                                </span>
                            @elseif($o->payment_status === 'rejected')
                                <span class="text-red-600 font-bold text-[11px]">Ditolak</span>
                            @else
                                <span class="text-amber-700 font-bold text-[11px]">Belum Bayar</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('admin.orders.invoice-pdf', $o->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-900 hover:text-white border border-slate-300 text-slate-800 text-[10px] font-semibold uppercase transition" title="Unduh Faktur PDF Resmi">
                                <i class="fa-solid fa-file-pdf text-red-600"></i>
                                <span>PDF</span>
                            </a>
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('admin.orders.show', $o->id) }}" class="px-3 py-1 bg-slate-900 text-white font-semibold text-[11px] uppercase hover:bg-slate-800 transition">
                                Kelola
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-slate-400">Tidak ada transaksi yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $orders->links() }}
    </div>

</div>
@endsection

