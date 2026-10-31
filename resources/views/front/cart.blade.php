@extends('layouts.app')

@section('content')
<div class="border-b border-neutral-200 bg-neutral-50/60 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold">Ringkasan Belanja</span>
        <h1 class="text-3xl font-extrabold text-neutral-950 tracking-tight mt-1">Keranjang Belanja Anda</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12">
    @if(empty($cart))
        <div class="text-center py-20 border border-dashed border-neutral-200 bg-neutral-50/50 rounded-2xl">
            <i class="fa-solid fa-bag-shopping text-4xl text-neutral-300 mb-4 block"></i>
            <h3 class="text-lg font-bold text-neutral-900 tracking-tight">Keranjang Anda masih kosong</h3>
            <p class="text-xs text-neutral-500 mt-1">Silakan pilih sepatu branded favorit Anda di katalog PasarKita.</p>
            <a href="{{ route('catalog') }}" class="inline-block mt-6 bg-neutral-950 text-white px-8 py-3.5 text-xs uppercase tracking-widest font-bold rounded-xl hover:bg-[#0000FF] shadow-md hover:shadow-lg active:scale-[0.98] transition cursor-pointer">
                Mulai Berbelanja &rarr;
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Items Table -->
            <div class="lg:col-span-2 space-y-6">
                <div class="border border-neutral-200 rounded-2xl overflow-hidden divide-y divide-neutral-100 bg-white">
                    <div class="p-4 bg-neutral-50/80 text-[11px] uppercase tracking-wider font-bold text-neutral-400 grid grid-cols-12 gap-4">
                        <div class="col-span-6">Produk & Varian</div>
                        <div class="col-span-2 text-center">Harga</div>
                        <div class="col-span-2 text-center">Jumlah</div>
                        <div class="col-span-2 text-right">Subtotal</div>
                    </div>

                    @foreach($cart as $item)
                        <div class="p-4 grid grid-cols-12 gap-4 items-center">
                            <!-- Produk Info -->
                            <div class="col-span-6 flex gap-4">
                                <div class="w-16 h-20 bg-[#FAFAFA] rounded-xl border border-neutral-200 flex items-center justify-center shrink-0 overflow-hidden p-1.5">
                                    @if(!empty($item['image']))
                                        <img src="{{ str_starts_with($item['image'], 'http') ? $item['image'] : (str_starts_with($item['image'], 'images/') ? asset($item['image']) : asset('storage/' . $item['image'])) }}" alt="{{ $item['product_name'] }}" class="w-full h-full object-contain">
                                    @else
                                        <i class="fa-solid fa-shoe-prints text-neutral-300"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] uppercase font-bold text-neutral-400 block">{{ $item['brand_name'] }}</span>
                                    <h4 class="text-xs font-bold text-neutral-900 truncate">{{ $item['product_name'] }}</h4>
                                    <p class="text-[11px] text-neutral-500 mt-0.5">
                                        {{ $item['model_name'] }} &bull; {{ $item['color_name'] }} &bull; {{ $item['size_name'] }}
                                    </p>
                                    @if(!empty($item['bonus_item']))
                                        <span class="inline-block mt-1 bg-neutral-100 text-neutral-900 text-[10px] px-2 py-0.5 rounded-md border border-neutral-200 font-medium">
                                            Bonus: {{ $item['bonus_item'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Harga Satuan -->
                            <div class="col-span-2 text-center text-xs text-neutral-700 font-medium">
                                Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                            </div>

                            <!-- Kuantitas -->
                            <div class="col-span-2 flex justify-center">
                                <div class="text-xs font-semibold px-2.5 py-1 border border-neutral-200 rounded-lg">
                                    {{ $item['quantity'] }}
                                </div>
                            </div>

                            <!-- Subtotal & Hapus -->
                            <div class="col-span-2 text-right">
                                <span class="text-xs font-bold text-neutral-950 block">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </span>
                                <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="cart_key" value="{{ $item['cart_key'] }}">
                                    <button type="submit" class="text-[11px] text-neutral-400 hover:text-red-600 transition mt-1 cursor-pointer">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center pt-2">
                    <a href="{{ route('catalog') }}" class="text-xs uppercase tracking-wider font-bold text-neutral-600 hover:text-neutral-950 transition">
                        &larr; Lanjutkan Berbelanja
                    </a>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="space-y-6">
                <div class="border border-neutral-200 p-6 bg-neutral-50/70 rounded-2xl space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-950 border-b border-neutral-200 pb-3">Ringkasan Pesanan</h3>
                    
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between text-neutral-600">
                            <span>Total Item</span>
                            <span class="font-semibold text-neutral-900">{{ array_sum(array_column($cart, 'quantity')) }} pasang</span>
                        </div>
                        <div class="flex justify-between text-neutral-600">
                            <span>Total Berat</span>
                            <span class="font-semibold text-neutral-900">{{ $totalWeight }} gram</span>
                        </div>
                        <div class="flex justify-between text-neutral-600 pt-2 border-t border-neutral-200">
                            <span class="font-semibold text-neutral-900">Subtotal</span>
                            <span class="font-bold text-neutral-950 text-sm">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <p class="text-[11px] text-neutral-500 leading-relaxed pt-2">
                        Pilihan skema Down Payment (DP) atau Pembayaran Penuh (100%), voucher diskon, dan pilihan kurir ekspedisi diterapkan di langkah checkout berikutnya.
                    </p>

                    <a href="{{ route('checkout.index') }}" 
                       class="w-full block text-center py-3.5 bg-neutral-950 text-white text-xs uppercase tracking-widest font-bold rounded-xl hover:bg-[#0000FF] transition-all shadow-sm hover:shadow-md active:scale-[0.98] cursor-pointer">
                        Lanjut ke Checkout &rarr;
                    </a>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection
