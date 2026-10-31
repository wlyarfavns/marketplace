@extends('layouts.app')

@section('content')
<div class="border-b border-neutral-200 bg-neutral-50/60 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold">Proses Pesanan</span>
        <h1 class="text-3xl font-extrabold text-neutral-950 tracking-tight mt-1">Checkout & Pengiriman</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12" 
     x-data="checkoutState({
        subtotal: {{ $subtotal }},
        totalQty: {{ $totalQuantity }},
        maxDpPercent: {{ $maxDpPercentage }},
        defaultSeller: '{{ $user ? $user->default_seller_code : '' }}',
        defaultCourier: '{{ $user ? $user->favorite_courier : 'JNE' }}'
     })">

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Kolom Kiri: Informasi Pembeli & Alamat (7 Cols) -->
            <div class="lg:col-span-7 space-y-8">
                
                <!-- Status User / Guest Notification -->
                @guest
                    <div class="bg-gray-50 border border-gray-200 p-4 text-xs flex justify-between items-center">
                        <div>
                            <span class="font-bold text-gray-900 uppercase">Checkout Cepat (Guest)</span>
                            <p class="text-gray-500 mt-0.5">Anda dapat berbelanja tanpa akun. Punya akun terdaftar?</p>
                        </div>
                        <a href="{{ route('login') }}" class="font-semibold underline uppercase tracking-wider text-black">Masuk</a>
                    </div>
                @endguest

                <!-- 1. Atribusi ID Seller Mitra (Opsional dengan Tombol Cek ID) -->
                <div class="border border-gray-200 p-6 space-y-3">
                    <label class="block text-xs uppercase font-bold text-gray-900 tracking-wider">
                        Kode Mitra Seller / Reseller (Opsional)
                    </label>
                    <p class="text-[11px] text-gray-500">
                        Masukkan kode ID Seller langganan Anda untuk menghubungkan pesanan ke mitra terkait.
                    </p>
                    <div class="flex gap-2">
                        <input type="text" name="seller_code" x-model="sellerCode" placeholder="Contoh: MITRA-001" 
                            class="flex-1 text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black uppercase">
                        <button type="button" @click="checkSeller()" class="bg-gray-100 hover:bg-black hover:text-white text-gray-800 text-xs px-5 py-2.5 font-semibold uppercase tracking-wider transition border border-gray-300">
                            Cek ID
                        </button>
                    </div>
                    <template x-if="sellerMessage">
                        <p class="text-xs font-semibold" :class="isSellerValid ? 'text-black' : 'text-red-500'" x-text="sellerMessage"></p>
                    </template>
                </div>

                <!-- 2. Data Penerima & Pengiriman -->
                <div class="border border-gray-200 p-6 space-y-6">
                    <h3 class="text-sm font-serif font-bold uppercase tracking-wider text-gray-950 border-b border-gray-100 pb-3">
                        Detail Penerima & Alamat Pengiriman
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-700">Nama Depan *</label>
                            <input type="text" name="recipient_first_name" required value="{{ old('recipient_first_name', $user ? explode(' ', $user->name)[0] : '') }}"
                                class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-700">Nama Belakang</label>
                            <input type="text" name="recipient_last_name" value="{{ old('recipient_last_name') }}"
                                class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-700">Nomor WhatsApp Aktif *</label>
                            <input type="text" name="whatsapp" required placeholder="08xxxxxxxxxx" value="{{ old('whatsapp', $user ? $user->phone : '') }}"
                                class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                            <p class="text-[10px] text-gray-400">Untuk konfirmasi & pelacakan status pesanan.</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-700">Pilihan Ekspedisi *</label>
                            <select name="courier_name" required x-model="selectedCourier" class="w-full text-xs px-3 py-2.5 border border-gray-300 bg-white focus:outline-none focus:border-black">
                                <option value="JNE">JNE Express</option>
                                <option value="SiCepat">SiCepat Cargo / Reguler</option>
                                <option value="J&T">J&T Express</option>
                                <option value="POS">POS Indonesia</option>
                                <option value="Anteraja">Anteraja</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-gray-700">Alamat Lengkap *</label>
                        <textarea name="address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan..."
                            class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">{{ old('address', $user ? $user->address : '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-700">Kota / Kabupaten *</label>
                            <input type="text" name="city" required value="{{ old('city', $user ? $user->city : '') }}"
                                class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-700">Provinsi *</label>
                            <input type="text" name="province" required value="{{ old('province', $user ? $user->province : '') }}"
                                class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-gray-700">Kode Pos *</label>
                            <input type="text" name="postal_code" required value="{{ old('postal_code', $user ? $user->postal_code : '') }}"
                                class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                        </div>
                    </div>

                    <!-- Kirim ke Alamat Berbeda Toggle -->
                    <div class="pt-4 border-t border-gray-100">
                        <label class="flex items-center gap-2 cursor-pointer text-xs">
                            <input type="checkbox" name="different_shipping" value="1" x-model="differentShipping" class="text-black focus:ring-0">
                            <span class="font-medium text-gray-800">Kirim ke alamat yang berbeda (Dropship / Hadiah)</span>
                        </label>

                        <div x-show="differentShipping" x-cloak class="mt-4 pt-4 border-t border-gray-100 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-gray-700">Nama Penerima Tujuan</label>
                                    <input type="text" name="shipping_first_name" class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-gray-700">Kota Tujuan</label>
                                    <input type="text" name="shipping_city" class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-gray-700">Alamat Tujuan Lengkap</label>
                                <textarea name="shipping_address" rows="2" class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1 pt-2">
                        <label class="text-xs font-semibold text-gray-700">Catatan Khusus Pesanan</label>
                        <input type="text" name="customer_notes" placeholder="Contoh: Titip di pos satpam jika tidak ada orang..."
                            class="w-full text-xs px-3 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                    </div>
                </div>

                <!-- 3. Skema Pilihan Pembayaran (DP vs Full Payment) -->
                <div class="border border-gray-200 p-6 space-y-4">
                    <h3 class="text-sm font-serif font-bold uppercase tracking-wider text-gray-950 border-b border-gray-100 pb-3">
                        Pilihan Metode Pembayaran
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Pilihan Bayar Penuh 100% -->
                        <label class="p-4 border cursor-pointer transition flex flex-col justify-between"
                               :class="paymentMethod === 'full' ? 'border-black bg-gray-50 ring-1 ring-black' : 'border-gray-200 hover:border-gray-300'">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-gray-900">Pembayaran Penuh</span>
                                    <input type="radio" name="payment_method_type" value="full" x-model="paymentMethod" @change="recalculateTotals()" class="text-black focus:ring-0">
                                </div>
                                <p class="text-[11px] text-gray-500 mt-2 leading-relaxed">
                                    Lunasi 100% total belanja sekarang. Pesanan langsung diproses tanpa tanggungan sisa tagihan.
                                </p>
                            </div>
                            <div class="pt-4 border-t border-gray-200 mt-4">
                                <span class="text-xs font-semibold text-gray-900">Bayar: 100%</span>
                            </div>
                        </label>

                        <!-- Pilihan Down Payment (DP) -->
                        <label class="p-4 border cursor-pointer transition flex flex-col justify-between"
                               :class="paymentMethod === 'dp' ? 'border-black bg-gray-50 ring-1 ring-black' : 'border-gray-200 hover:border-gray-300'">
                            <div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold uppercase tracking-wider text-gray-900">Uang Muka (DP)</span>
                                        <span class="bg-black text-white text-[9px] font-bold px-1.5 py-0.5" x-text="maxDpPercent + '%'"></span>
                                    </div>
                                    <input type="radio" name="payment_method_type" value="dp" x-model="paymentMethod" @change="recalculateTotals()" class="text-black focus:ring-0">
                                </div>
                                <p class="text-[11px] text-gray-500 mt-2 leading-relaxed">
                                    Bayar DP sesuai persentase brand (<span x-text="maxDpPercent + '%'"></span>). Sisa tagihan dibayarkan sebelum barang dikirim.
                                </p>
                            </div>
                            <div class="pt-4 border-t border-gray-200 mt-4 flex justify-between text-xs font-semibold">
                                <span>DP Sekarang:</span>
                                <span class="text-gray-900 font-bold" x-text="formatRupiah(dpAmount)"></span>
                            </div>
                        </label>

                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Ringkasan Belanja, Kupon, & Tombol Buat Pesanan (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">
                
                <div class="border border-gray-200 p-6 bg-gray-50 space-y-6">
                    <h3 class="text-sm font-serif font-bold uppercase tracking-wider text-gray-950 border-b border-gray-200 pb-3">
                        Ringkasan Belanja ({{ $totalQuantity }} item)
                    </h3>

                    <!-- Daftar Item Ringkas -->
                    <div class="space-y-3 max-h-60 overflow-y-auto divide-y divide-gray-100 pr-1">
                        @foreach($cart as $item)
                            <div class="pt-2 first:pt-0 flex items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-10 h-12 bg-neutral-100 border border-gray-200 shrink-0 overflow-hidden flex items-center justify-center">
                                        @if(!empty($item['image']))
                                            <img src="{{ str_starts_with($item['image'], 'http') ? $item['image'] : (str_starts_with($item['image'], 'images/') ? asset($item['image']) : asset('storage/' . $item['image'])) }}" alt="{{ $item['product_name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fa-solid fa-shoe-prints text-[10px] text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate w-40">{{ $item['product_name'] }}</p>
                                        <p class="text-[10px] text-gray-400">
                                            {{ $item['quantity'] }}x &bull; {{ $item['model_name'] }}, {{ $item['color_name'] }}, {{ $item['size_name'] }}
                                        </p>
                                    </div>
                                </div>
                                <span class="font-semibold text-gray-900 shrink-0">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Input Kupon Diskon -->
                    <div class="pt-4 border-t border-gray-200 space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-900">Kode Kupon Diskon</label>
                        <div class="flex gap-2">
                            <input type="text" name="coupon_code" x-model="couponCode" placeholder="Masukkan kode kupon" 
                                class="flex-1 text-xs px-3 py-2 border border-gray-300 focus:outline-none focus:border-black uppercase">
                            <button type="button" @click="checkCoupon()" class="bg-gray-200 hover:bg-black hover:text-white text-black text-xs px-4 py-2 font-semibold uppercase tracking-wider transition">
                                Pakai
                            </button>
                        </div>
                        <template x-if="couponMessage">
                            <p class="text-xs font-semibold" :class="isCouponValid ? 'text-black' : 'text-red-500'" x-text="couponMessage"></p>
                        </template>

                        <!-- Kupon promo yang tersedia -->
                        @if($availableCoupons->count() > 0)
                            <div class="pt-2">
                                <p class="text-[10px] text-gray-400 font-semibold uppercase mb-1">Kupon Tersedia:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($availableCoupons as $ac)
                                        <button type="button" @click="couponCode = '{{ $ac->code }}'; checkCoupon()" class="text-[10px] border border-gray-300 bg-white px-2 py-0.5 text-gray-700 hover:border-black">
                                            {{ $ac->code }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Kalkulasi Total Biaya -->
                    <div class="pt-4 border-t border-gray-200 space-y-2.5 text-xs">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Produk</span>
                            <span class="font-semibold text-gray-900" x-text="formatRupiah(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600" x-show="discountAmount > 0">
                            <span>Potongan Kupon</span>
                            <span class="font-bold text-black" x-text="'- ' + formatRupiah(discountAmount)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Ongkos Kirim (Ekspedisi)</span>
                            <span class="font-semibold text-gray-900">Rp 0 (Promo)</span>
                        </div>
                        <div class="flex justify-between text-gray-900 pt-3 border-t border-gray-200 text-sm font-bold">
                            <span>Grand Total</span>
                            <span x-text="formatRupiah(grandTotal)"></span>
                        </div>

                        <!-- Skema DP jika dipilih -->
                        <div x-show="paymentMethod === 'dp'" x-cloak class="mt-3 p-3 bg-white border border-gray-300 space-y-1.5 text-xs">
                            <div class="flex justify-between font-bold text-gray-900">
                                <span>Tagihan DP (Dibayar Sekarang):</span>
                                <span class="text-black" x-text="formatRupiah(dpAmount)"></span>
                            </div>
                            <div class="flex justify-between text-[11px] text-gray-500">
                                <span>Sisa Pelunasan:</span>
                                <span x-text="formatRupiah(remainingAmount)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Rekening Tujuan Transfer Bank -->
                    <div class="pt-4 border-t border-gray-200 space-y-2">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-900">Transfer Bank Resmi PasarKita:</p>
                        <div class="space-y-1.5 text-xs text-gray-600">
                            @foreach($banks as $b)
                                <div class="bg-white p-2.5 border border-gray-200 flex justify-between items-center">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $b->bank_name }}</p>
                                        <p class="font-mono text-xs">{{ $b->account_number }}</p>
                                    </div>
                                    <span class="text-[10px] text-gray-400">{{ $b->account_holder_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-4 bg-neutral-950 text-white text-xs uppercase tracking-widest font-black rounded-xl hover:bg-[#0000FF] transition-all shadow-md hover:shadow-lg active:scale-[0.98] cursor-pointer">
                        Buat Pesanan Sekarang &rarr;
                    </button>

                    <p class="text-[10px] text-gray-400 text-center leading-relaxed">
                        Dengan menekan tombol, Anda menyetujui syarat & ketentuan transaksi PasarKita.
                    </p>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
    function checkoutState(config) {
        return {
            subtotal: config.subtotal,
            totalQty: config.totalQty,
            maxDpPercent: config.maxDpPercent,
            sellerCode: config.defaultSeller,
            selectedCourier: config.defaultCourier,
            differentShipping: false,

            sellerMessage: '',
            isSellerValid: false,

            couponCode: '',
            couponMessage: '',
            isCouponValid: false,
            discountAmount: 0,

            paymentMethod: 'full',

            get grandTotal() {
                return Math.max(0, this.subtotal - this.discountAmount);
            },
            get dpAmount() {
                return Math.round((this.maxDpPercent / 100) * this.grandTotal);
            },
            get remainingAmount() {
                return this.grandTotal - this.dpAmount;
            },
            formatRupiah(num) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
            },
            checkSeller() {
                if (!this.sellerCode) {
                    this.sellerMessage = 'Masukkan kode seller terlebih dahulu.';
                    this.isSellerValid = false;
                    return;
                }
                fetch('{{ route("ajax.check-seller") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ seller_code: this.sellerCode })
                })
                .then(res => res.json())
                .then(data => {
                    this.isSellerValid = data.valid;
                    this.sellerMessage = data.message;
                })
                .catch(() => {
                    this.isSellerValid = false;
                    this.sellerMessage = 'Kode seller tidak ditemukan.';
                });
            },
            checkCoupon() {
                if (!this.couponCode) {
                    this.couponMessage = 'Masukkan kode kupon terlebih dahulu.';
                    this.isCouponValid = false;
                    return;
                }
                fetch('{{ route("ajax.check-coupon") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        code: this.couponCode,
                        subtotal: this.subtotal,
                        total_quantity: this.totalQty
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.isCouponValid = data.valid;
                    this.couponMessage = data.message;
                    if (data.valid) {
                        this.discountAmount = data.discount_amount;
                    } else {
                        this.discountAmount = 0;
                    }
                })
                .catch(() => {
                    this.isCouponValid = false;
                    this.couponMessage = 'Kupon tidak valid atau telah kadaluarsa.';
                    this.discountAmount = 0;
                });
            },
            recalculateTotals() {
                // reactive getters
            }
        }
    }
    window.checkoutState = checkoutState;
    document.addEventListener('alpine:init', () => {
        if (typeof Alpine !== 'undefined' && Alpine.data) {
            Alpine.data('checkoutState', checkoutState);
        }
    });
</script>
@endsection

