@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-black text-white text-2xl mb-4 shadow-md">
            <i class="fa-solid fa-check"></i>
        </div>
        <h1 class="text-3xl font-serif font-bold text-gray-950">Pesanan Berhasil Dibuat</h1>
        <p class="text-xs text-gray-500 mt-2">Nomor Pesanan Unik: <span class="font-mono font-bold text-black text-sm">{{ $order->order_number }}</span></p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-8 py-12 space-y-10">

    <!-- Status Pembayaran Box -->
    <div class="border border-gray-200 p-6 bg-white flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="text-[10px] uppercase tracking-wider font-semibold text-gray-400 block mb-1">Status Pembayaran Saat Ini</span>
            <div class="flex items-center gap-2">
                @if($order->payment_status === 'unpaid')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold uppercase">
                        <i class="fa-regular fa-clock"></i> Menunggu Transfer Pembayaran
                    </span>
                @elseif($order->payment_status === 'waiting_verification')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-neutral-100 text-neutral-800 border border-neutral-300 text-xs font-semibold uppercase">
                        <i class="fa-solid fa-hourglass-half"></i> Menunggu Verifikasi Admin
                    </span>
                @elseif($order->payment_status === 'verified')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-black text-white border border-black text-xs font-semibold uppercase">
                        <i class="fa-solid fa-circle-check"></i> Terverifikasi (Lunas / DP Diterima)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-800 border border-red-200 text-xs font-semibold uppercase">
                        Ditolak
                    </span>
                @endif

                <span class="text-xs text-gray-500 font-medium">
                    Skema: {{ strtoupper($order->payment_method_type) }}
                </span>
            </div>
        </div>

        <div class="text-right">
            <span class="text-[10px] uppercase tracking-wider font-semibold text-gray-400 block mb-1">Jumlah Yang Harus Ditransfer</span>
            <span class="text-2xl font-bold text-gray-950">
                @if($order->payment_method_type === 'dp')
                    Rp {{ number_format($order->dp_amount, 0, ',', '.') }}
                    <span class="block text-[11px] font-normal text-gray-500 mt-0.5">
                        (DP Sekarang, Sisa Tagihan Rp {{ number_format($order->remaining_amount, 0, ',', '.') }})
                    </span>
                @else
                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                @endif
            </span>
        </div>
    </div>

    <!-- Rincian Rekening Bank Resmi -->
    <div class="border border-gray-200 p-6 bg-gray-50 space-y-4">
        <h3 class="text-xs uppercase font-bold tracking-wider text-gray-900 border-b border-gray-200 pb-2">
            Silakan Transfer Tepat ke Salah Satu Rekening Resmi PasarKita:
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($banks as $b)
                <div class="bg-white p-4 border border-gray-200 space-y-1">
                    <p class="text-xs uppercase font-bold text-gray-900">{{ $b->bank_name }}</p>
                    <p class="font-mono text-base font-bold text-gray-900 select-all">{{ $b->account_number }}</p>
                    <p class="text-[11px] text-gray-500">a/n {{ $b->account_holder_name }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Form Unggah / Ganti Bukti Transfer Bank -->
    <div class="border border-gray-200 p-6 bg-white space-y-6">
        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
            <div>
                <h3 class="text-sm font-serif font-bold uppercase tracking-wider text-gray-950">Unggah Bukti Transfer</h3>
                <p class="text-xs text-gray-500 mt-0.5">Mendukung format JPG, PNG, atau PDF (maksimal 2 MB)</p>
            </div>
            @if($order->paymentConfirmations->count() > 0)
                <span class="text-[11px] text-gray-400">Bukti telah diunggah {{ $order->paymentConfirmations->count() }}x</span>
            @endif
        </div>

        <form action="{{ route('checkout.upload-proof', $order->order_number) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Bank Tujuan Transfer *</label>
                    <select name="bank_account_id" required class="w-full text-xs px-3 py-2.5 border border-gray-300 bg-white focus:outline-none focus:border-black">
                        @foreach($banks as $b)
                            <option value="{{ $b->id }}">{{ $b->bank_name }} — {{ $b->account_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Tahap Pembayaran *</label>
                    <select name="payment_stage" required class="w-full text-xs px-3 py-2.5 border border-gray-300 bg-white focus:outline-none focus:border-black">
                        @if($order->payment_method_type === 'dp')
                            <option value="dp">Pembayaran DP Awal (Rp {{ number_format($order->dp_amount, 0, ',', '.') }})</option>
                            <option value="remaining">Pelunasan Sisa (Rp {{ number_format($order->remaining_amount, 0, ',', '.') }})</option>
                        @else
                            <option value="full">Pembayaran Penuh 100% (Rp {{ number_format($order->grand_total, 0, ',', '.') }})</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Nominal Transfer (Rp) *</label>
                    <input type="number" name="amount" required 
                           value="{{ $order->payment_method_type === 'dp' ? $order->dp_amount : $order->grand_total }}"
                           class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Pilih File Bukti (JPG, PNG, PDF max 2MB) *</label>
                    <input type="file" name="proof_file" required accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full text-xs px-3 py-2 border border-gray-300 bg-white focus:outline-none focus:border-black file:mr-3 file:py-1 file:px-3 file:border-0 file:text-xs file:bg-black file:text-white file:cursor-pointer">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-700">Catatan Pengirim / Pemilik Rekening</label>
                <input type="text" name="notes" placeholder="Contoh: Transfer atas nama Ibu Fatimah BCA"
                       class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
            </div>

            <button type="submit" class="bg-black text-white text-xs uppercase tracking-widest font-semibold px-8 py-3.5 hover:bg-black transition">
                {{ $order->paymentConfirmations->count() > 0 ? 'Ganti / Unggah Ulang Bukti Pembayaran' : 'Unggah Bukti Transfer' }}
            </button>
        </form>

        <!-- Riwayat Unggahan Bukti Sebelumnya -->
        @if($order->paymentConfirmations->count() > 0)
            <div class="pt-6 border-t border-gray-100 space-y-3">
                <h4 class="text-xs uppercase font-bold text-gray-700">Riwayat Unggahan Bukti Transfer:</h4>
                <div class="space-y-2">
                    @foreach($order->paymentConfirmations as $pc)
                        <div class="p-3 bg-gray-50 border border-gray-200 flex justify-between items-center text-xs">
                            <div>
                                <span class="font-bold text-gray-900 uppercase">Tahap: {{ $pc->payment_stage }}</span> &bull; 
                                <span>Nominal: Rp {{ number_format($pc->amount, 0, ',', '.') }}</span> &bull; 
                                <span class="text-gray-400">{{ $pc->created_at->format('d/m/Y H:i') }}</span>
                                @if($pc->notes)
                                    <p class="text-[11px] text-gray-500 mt-0.5">Catatan: {{ $pc->notes }}</p>
                                @endif
                            </div>
                            <div>
                                @if($pc->verification_status === 'pending')
                                    <span class="text-[10px] uppercase font-semibold text-gray-900 bg-gray-100 px-2 py-0.5 border border-gray-300">Menunggu Verifikasi</span>
                                @elseif($pc->verification_status === 'verified')
                                    <span class="text-[10px] uppercase font-semibold text-black bg-neutral-200 px-2 py-0.5 border border-neutral-400">Disetujui Admin</span>
                                @else
                                    <span class="text-[10px] uppercase font-semibold text-red-700 bg-red-50 px-2 py-0.5 border border-red-200">Ditolak</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Ringkasan Produk Pesanan -->
    <div class="border border-gray-200 p-6 bg-white space-y-4">
        <h3 class="text-sm font-serif font-bold uppercase tracking-wider text-gray-950 border-b border-gray-100 pb-3">Rincian Item Pesanan</h3>
        <div class="divide-y divide-gray-100 text-xs">
            @foreach($order->items as $item)
                <div class="py-3 flex justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-14 bg-neutral-100 border border-gray-200 shrink-0 overflow-hidden flex items-center justify-center">
                            @if($item->product && $item->product->primary_image_url)
                                <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product_name_snapshot }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-shoe-prints text-xs text-gray-400"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $item->product_name_snapshot }}</p>
                            <p class="text-gray-500 text-[11px]">
                                Model: {{ $item->model_name }} &bull; Warna: {{ $item->color_name }} &bull; Ukuran: {{ $item->size_name }}
                            </p>
                            @if($item->bonus_item_snapshot)
                                <span class="text-[10px] text-black bg-neutral-100 px-1.5 py-0.5 border border-gray-300 inline-block mt-1 font-medium">
                                    Hadiah: {{ $item->bonus_item_snapshot }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-gray-600">{{ $item->quantity }} &times; Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                        <p class="font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-gray-200 space-y-2 text-xs">
            <div class="flex justify-between text-gray-600">
                <span>Penerima</span>
                <span class="font-medium text-gray-900">{{ $order->recipient_first_name }} {{ $order->recipient_last_name }} ({{ $order->whatsapp }})</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Alamat Tujuan</span>
                <span class="font-medium text-gray-900">{{ $order->address }}, {{ $order->city }}, {{ $order->province }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Ekspedisi Pengiriman</span>
                <span class="font-medium text-gray-900">{{ $order->courier_name }}</span>
            </div>
            @if($order->seller)
                <div class="flex justify-between text-gray-600">
                    <span>Mitra Seller Terhubung</span>
                    <span class="font-medium text-gray-900">{{ $order->seller->name }} ({{ $order->seller->seller_code }})</span>
                </div>
            @endif
        </div>
    </div>

    <div class="text-center pt-4">
        <a href="{{ route('home') }}" class="inline-block border border-black text-black px-8 py-3 text-xs uppercase tracking-widest font-semibold hover:bg-black hover:text-white transition">
            Kembali ke Beranda
        </a>
    </div>

</div>
@endsection

