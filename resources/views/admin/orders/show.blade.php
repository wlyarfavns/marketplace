@extends('layouts.admin')

@section('page_title', 'Detail Pesanan: ' . $order->order_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="text-xs font-mono font-bold text-slate-500 uppercase">{{ $order->order_number }}</span>
            <h2 class="text-xl font-bold text-slate-900 mt-0.5">Kelola Transaksi & Pembayaran</h2>
            <p class="text-xs text-slate-500">Dibuat pada {{ $order->created_at->format('d F Y, H:i:s') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.invoice-pdf', $order->id) }}" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold uppercase px-4 py-2 transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Unduh Faktur PDF (FKT-...)
            </a>
            <a href="{{ route('admin.orders.index') }}" class="border border-slate-300 text-slate-700 text-xs font-semibold uppercase px-4 py-2 hover:bg-slate-50 transition">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Panel Ubah Status Cepat -->
    <div class="bg-white border border-slate-200 p-6 space-y-4">
        <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 border-b border-slate-100 pb-2">Status Operasional & Finansial</h3>
        
        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Status Pesanan</label>
                <select name="order_status" class="w-full px-3 py-2 border border-slate-300 bg-white font-bold">
                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Diproses (Packing)</option>
                    <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Dikirim (Ekspedisi)</option>
                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Status Pembayaran</label>
                <select name="payment_status" class="w-full px-3 py-2 border border-slate-300 bg-white font-bold">
                    <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Belum Bayar (Unpaid)</option>
                    <option value="waiting_verification" {{ $order->payment_status === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="verified" {{ $order->payment_status === 'verified' ? 'selected' : '' }}>Terverifikasi (Verified)</option>
                    <option value="rejected" {{ $order->payment_status === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="font-semibold text-slate-700">Status Down Payment (DP)</label>
                <select name="dp_status" class="w-full px-3 py-2 border border-slate-300 bg-white font-bold">
                    <option value="none" {{ $order->dp_status === 'none' ? 'selected' : '' }}>None (Belum DP)</option>
                    <option value="dp_paid" {{ $order->dp_status === 'dp_paid' ? 'selected' : '' }}>DP Terbayar (Sisa Belum Lunas)</option>
                    <option value="fully_paid" {{ $order->dp_status === 'fully_paid' ? 'selected' : '' }}>Lunas Penuh (Fully Paid)</option>
                </select>
            </div>

            <div class="sm:col-span-3 flex justify-end">
                <button type="submit" class="bg-slate-900 text-white font-bold px-6 py-2 uppercase tracking-wider text-xs hover:bg-slate-800 transition">
                    Simpan Perubahan Status
                </button>
            </div>
        </form>
    </div>

    <!-- Modul Verifikasi Bukti Transfer Sesuai PRD Poin 4.2.6 -->
    <div class="bg-white border border-slate-200 p-6 space-y-4">
        <div class="border-b border-slate-100 pb-2 flex justify-between items-center">
            <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900">Verifikasi Berkas Bukti Transfer Bank</h3>
            <span class="text-xs text-slate-400">{{ $order->paymentConfirmations->count() }} Bukti Diunggah</span>
        </div>

        @if($order->paymentConfirmations->isEmpty())
            <div class="p-6 bg-slate-50 border border-slate-200 text-center text-xs text-slate-400">
                Pembeli belum mengunggah berkas bukti transfer untuk pesanan ini.
            </div>
        @else
            <div class="space-y-4">
                @foreach($order->paymentConfirmations as $conf)
                    <div class="p-4 border border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 uppercase">Tahap: {{ $conf->payment_stage }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($conf->amount, 0, ',', '.') }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-slate-500">{{ $conf->bankAccount ? $conf->bankAccount->bank_name : 'Transfer Bank' }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Waktu Unggah: {{ $conf->created_at->format('d/m/Y H:i') }} &bull; File: 
                                <a href="{{ asset('storage/' . $conf->proof_file_path) }}" target="_blank" class="font-bold underline text-blue-600">Buka Berkas Bukti &rarr;</a>
                            </p>
                            @if($conf->notes)
                                <p class="text-[11px] text-slate-600 italic">"{{ $conf->notes }}"</p>
                            @endif
                        </div>

                        <!-- Tombol Verifikasi Cepat -->
                        <div class="flex items-center gap-2">
                            @if($conf->verification_status === 'pending')
                                <form action="{{ route('admin.orders.verify-payment', [$order->id, $conf->id]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="verify">
                                    <button type="submit" class="bg-emerald-600 text-white font-bold px-3 py-1.5 uppercase text-[10px] hover:bg-emerald-700 transition">
                                        <i class="fa-solid fa-check"></i> Setujui & Verifikasi
                                    </button>
                                </form>
                                <form action="{{ route('admin.orders.verify-payment', [$order->id, $conf->id]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="bg-red-600 text-white font-bold px-3 py-1.5 uppercase text-[10px] hover:bg-red-700 transition">
                                        <i class="fa-solid fa-xmark"></i> Tolak
                                    </button>
                                </form>
                            @elseif($conf->verification_status === 'verified')
                                <span class="bg-emerald-100 text-emerald-800 font-bold px-3 py-1 uppercase text-[10px] border border-emerald-300">
                                    Disetujui
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 font-bold px-3 py-1 uppercase text-[10px] border border-red-300">
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Rincian Item Pesanan -->
    <div class="bg-white border border-slate-200 p-6 space-y-4 text-xs">
        <h3 class="text-xs uppercase font-bold tracking-wider text-slate-900 border-b border-slate-100 pb-2">Item Pembelian</h3>

        <div class="divide-y divide-slate-100">
            @foreach($order->items as $it)
                <div class="py-3 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-slate-900 text-sm">{{ $it->product_name_snapshot }}</p>
                        <p class="text-slate-500 text-[11px] mt-0.5">
                            Model: {{ $it->model_name }} &bull; Warna: {{ $it->color_name }} &bull; Ukuran: {{ $it->size_name }}
                        </p>
                        @if($it->bonus_item_snapshot)
                            <span class="text-[10px] bg-green-50 text-green-700 border border-green-200 px-1.5 py-0.5 font-bold inline-block mt-1">
                                Bonus FREE Item: {{ $it->bonus_item_snapshot }}
                            </span>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-slate-500">{{ $it->quantity }} &times; Rp {{ number_format($it->unit_price, 0, ',', '.') }}</p>
                        <p class="font-bold text-slate-900 text-sm">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-slate-200 space-y-1.5 text-xs text-right">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal Item</span>
                <span class="font-semibold text-slate-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount > 0)
                <div class="flex justify-between text-emerald-600">
                    <span>Diskon Kupon</span>
                    <span class="font-semibold">- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between text-slate-600">
                <span>Ongkir ({{ $order->courier_name }})</span>
                <span class="font-semibold text-slate-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-base font-bold text-slate-950 pt-2 border-t border-slate-200">
                <span>Grand Total</span>
                <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>

            @if($order->payment_method_type === 'dp')
                <div class="p-3 bg-slate-50 border border-slate-200 text-left mt-2 space-y-1">
                    <div class="flex justify-between font-bold text-slate-900">
                        <span>Nominal DP:</span>
                        <span>Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 text-[11px]">
                        <span>Sisa Tagihan Pelunasan:</span>
                        <span>Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Data Penerima & Mitra Seller -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
        <div class="bg-white border border-slate-200 p-6 space-y-2">
            <h4 class="font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">Informasi Penerima</h4>
            <p class="font-bold text-slate-800 text-sm">{{ $order->recipient_first_name }} {{ $order->recipient_last_name }}</p>
            <p class="text-slate-600">WhatsApp: <span class="font-mono font-bold">{{ $order->whatsapp }}</span></p>
            <p class="text-slate-600 leading-relaxed">{{ $order->address }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}</p>
            @if($order->customer_notes)
                <p class="text-slate-500 italic pt-2">Catatan Pesanan: "{{ $order->customer_notes }}"</p>
            @endif
        </div>

        <div class="bg-white border border-slate-200 p-6 space-y-2">
            <h4 class="font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">Atribusi Kemitraan (Seller)</h4>
            @if($order->seller)
                <p class="font-bold text-slate-900 text-sm">{{ $order->seller->name }}</p>
                <p class="text-slate-600">Kode Mitra: <span class="font-mono font-bold bg-slate-100 px-2 py-0.5">{{ $order->seller->seller_code }}</span></p>
                <p class="text-slate-500">Brand Terkait: {{ $order->seller->brand ? $order->seller->brand->name : '-' }}</p>
                <p class="text-emerald-700 font-semibold text-[11px] pt-1">
                    <i class="fa-solid fa-circle-check"></i> Transaksi terhitung dalam omzet komisi seller ini.
                </p>
            @else
                <p class="text-slate-400 italic">Transaksi Checkout Mandiri (Tanpa Atribusi Kode ID Seller Mitra).</p>
            @endif
        </div>
    </div>

</div>
@endsection

