@extends('layouts.app')

@section('content')
<!-- 1. Hero Section Editorial Clean Minimalist (Dominan Putih & Hitam dengan Aksen Biru Lembut) -->
<section class="border-b border-neutral-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-16 lg:py-24 flex flex-col md:flex-row items-center justify-between gap-12">
        <div class="max-w-xl space-y-6">
            <span class="text-xs uppercase tracking-[0.25em] text-neutral-400 font-bold block">Branded Footwear & Sneakers Curation</span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-neutral-950 leading-[1.1] tracking-tight">
                Koleksi Sepatu Branded Original & Terkurasi.
            </h1>
            <p class="text-sm sm:text-base text-neutral-600 leading-relaxed font-normal">
                Destinasi resmi sneakers dan sepatu branded original dari Nike, Air Jordan, Adidas, New Balance, Puma, dan Asics. Hadir dengan jaminan 100% otentik, skema Ready Stock dan Pre-Order terpercaya ber-DP.
            </p>
            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a href="{{ route('catalog', ['type' => 'ready_stock']) }}" 
                   class="bg-[#0000FF] hover:bg-blue-700 text-white text-xs uppercase tracking-wider font-semibold px-8 py-3.5 rounded-xl transition-all duration-200 shadow-xs hover:shadow-md active:scale-[0.98] cursor-pointer inline-flex items-center gap-2">
                    <span>Jelajahi Ready Stock</span>
                    <i class="fa-solid fa-arrow-right text-[11px]"></i>
                </a>
                <a href="{{ route('catalog') }}" 
                   class="border border-neutral-300 bg-white hover:border-[#0000FF] hover:text-[#0000FF] text-neutral-800 text-xs uppercase tracking-wider font-semibold px-8 py-3.5 rounded-xl transition-all duration-200 shadow-2xs active:scale-[0.98] cursor-pointer">
                    Semua Koleksi
                </a>
            </div>
        </div>

        <div class="w-full md:w-5/12">
            <div class="relative bg-white border border-neutral-200/90 rounded-2xl p-6 sm:p-7 shadow-xs hover:border-neutral-400 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                <div class="relative aspect-[4/3] bg-[#FAFAFA] rounded-xl flex items-center justify-center p-5 overflow-hidden border border-neutral-100">
                    <img src="{{ asset('images/shoes/nike-dunk-panda.jpg') }}" 
                         alt="Nike Dunk Low Retro Panda" 
                         class="w-full h-full object-contain group-hover:scale-105 transition duration-500">
                    <span class="absolute top-3 left-3 bg-white/95 backdrop-blur-xs text-neutral-800 text-[10px] uppercase tracking-wider font-bold px-2.5 py-1 rounded-md border border-neutral-200/90 shadow-2xs">
                        Pilihan Editor
                    </span>
                </div>
                <div class="mt-5 space-y-2 text-left">
                    <span class="text-[10px] tracking-widest uppercase font-bold text-neutral-400">Sneakers Ikonik</span>
                    <h3 class="text-xl sm:text-2xl font-bold text-neutral-950 tracking-tight">Nike Dunk Low Retro "Panda"</h3>
                    <p class="text-xs text-neutral-500 leading-relaxed font-normal">Siluet street-classic paling versatile dengan perpaduan warna kontras hitam dan putih ikonik.</p>
                    <div class="pt-2">
                        <a href="{{ route('product.detail', 'nike-dunk-low-retro-panda') }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-3 bg-neutral-50 hover:bg-[#0000FF] text-neutral-800 hover:text-white border border-neutral-200/90 hover:border-[#0000FF] text-xs uppercase tracking-wider font-semibold rounded-xl transition-all duration-200 shadow-2xs hover:shadow-sm active:scale-[0.98] cursor-pointer">
                            <span>Lihat Detail Sepatu</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Grid Brand Sepatu Pilihan -->
<section class="py-16 border-b border-neutral-100 bg-neutral-50/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
            <div>
                <span class="text-[11px] uppercase tracking-widest font-bold text-neutral-400">Brand Resmi Terdaftar</span>
                <h2 class="text-2xl font-bold text-neutral-950 tracking-tight mt-1">Brand Pilihan di PasarKita</h2>
            </div>
            <a href="{{ route('catalog') }}" class="text-xs uppercase tracking-wider font-semibold border-b border-neutral-300 pb-0.5 text-neutral-700 hover:text-[#0000FF] hover:border-[#0000FF] transition">
                Lihat Seluruh Brand &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($brands as $b)
                <a href="{{ route('catalog', ['brand' => $b->slug]) }}" 
                   class="group bg-white p-6 rounded-2xl border border-neutral-200/90 hover:border-neutral-400 hover:shadow-md transition-all duration-200 flex flex-col justify-between h-44 shadow-2xs">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-neutral-400 font-bold block mb-1">Brand Mitra #{{ $loop->iteration }}</span>
                        <h3 class="text-lg font-bold text-neutral-950 group-hover:text-[#0000FF] transition-colors tracking-tight">{{ $b->name }}</h3>
                        <p class="text-xs text-neutral-500 line-clamp-2 mt-2 leading-relaxed">{{ $b->description }}</p>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-neutral-100 text-[11px] text-neutral-600 font-medium">
                        <span>DP Mulai {{ number_format($b->dp_percentage, 0) }}%</span>
                        <i class="fa-solid fa-arrow-right text-neutral-400 group-hover:text-[#0000FF] group-hover:translate-x-0.5 transition"></i>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- 3. Banner Promo (Dominan Putih Bersih & Elegan) -->
@if($banners->count() > 0)
<section class="py-14 border-b border-neutral-200 bg-neutral-50/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 space-y-6">
        @foreach($banners as $banner)
            <div class="relative w-full bg-white rounded-3xl border border-neutral-200/90 overflow-hidden flex items-center p-8 sm:p-12 md:p-14 shadow-2xs hover:border-neutral-400 hover:shadow-md transition-all duration-300 group min-h-[240px]">
                @if($banner->image_url)
                    <div class="absolute right-0 top-0 bottom-0 w-full sm:w-7/12 md:w-1/2 overflow-hidden">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent hidden sm:block"></div>
                        <div class="absolute inset-0 bg-white/90 sm:hidden"></div>
                    </div>
                @endif
                
                <div class="relative z-10 max-w-xl space-y-3.5">
                    <span class="inline-block text-[10px] tracking-widest uppercase font-bold text-neutral-700 bg-neutral-100 px-3 py-1 rounded-md border border-neutral-200/80">
                        Promo Eksklusif PasarKita
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-neutral-950 leading-tight tracking-tight">
                        {{ $banner->title }}
                    </h2>
                    <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed font-normal max-w-md">
                        {{ $banner->description }}
                    </p>
                    @if($banner->url)
                        <div class="pt-1">
                            <a href="{{ $banner->url }}" class="inline-flex items-center gap-2 bg-neutral-50 hover:bg-[#0000FF] text-neutral-800 hover:text-white border border-neutral-300 hover:border-[#0000FF] text-xs uppercase tracking-wider font-semibold px-7 py-3 rounded-xl transition-all duration-200 shadow-2xs hover:shadow-sm active:scale-[0.98] cursor-pointer">
                                <span>Klaim Penawaran</span>
                                <i class="fa-solid fa-arrow-right text-[11px]"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

<!-- 4. Koleksi Produk Pilihan (Adem, Soft, Minimalis Elegan) -->
<section class="py-20 border-b border-neutral-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 gap-4">
            <div>
                <span class="text-[11px] uppercase tracking-widest font-bold text-neutral-400">Koleksi Terkini</span>
                <h2 class="text-3xl font-extrabold text-neutral-950 tracking-tight mt-1">Koleksi Sepatu Branded Pilihan</h2>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('catalog', ['type' => 'ready_stock']) }}" class="px-4 py-2 rounded-xl border border-neutral-200 hover:border-neutral-400 transition font-semibold text-neutral-700 bg-neutral-50/60">Ready Stock</a>
                @auth
                    @if(in_array(Auth::user()->customer_type, ['member', 'distributor']))
                        <a href="{{ route('catalog', ['type' => 'po']) }}" class="px-4 py-2 rounded-xl border border-neutral-200 hover:border-neutral-400 transition font-semibold text-neutral-700 bg-neutral-50/60">Pre-Order (PO)</a>
                    @endif
                @endauth
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-7">
            @foreach($featuredProducts as $prod)
                <div class="group relative flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white hover:border-neutral-350 hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <div>
                        <!-- Thumbnail Box (Pure White Seamless Studio) -->
                        <div class="relative aspect-[4/3] bg-[#FAFAFA] p-4 sm:p-5 flex items-center justify-center overflow-hidden border-b border-neutral-100">
                            @if($prod->primary_image_url)
                                <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" 
                                     onerror="if (this.src !== '{{ $prod->primary_image_url }}') this.src = '{{ $prod->primary_image_url }}';"
                                     class="w-full h-full object-contain group-hover:scale-105 group-hover:-translate-y-1 transition-all duration-500 filter drop-shadow-xs">
                            @else
                                <i class="fa-solid fa-shoe-prints text-neutral-300 text-4xl group-hover:scale-110 transition duration-300"></i>
                            @endif
                            
                            <!-- Badges Adem & Soft -->
                            <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                                @if($prod->product_type === 'ready_stock')
                                    <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] uppercase font-semibold tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#0000FF]"></span>
                                        Ready Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] uppercase font-semibold tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
                                        <i class="fa-regular fa-clock text-[9px] text-neutral-400"></i>
                                        Pre-Order
                                    </span>
                                @endif

                                @if($prod->visible_to_distributor && !$prod->visible_to_umum && !$prod->visible_to_member)
                                    <span class="bg-neutral-100 text-neutral-700 text-[9px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-md border border-neutral-200">
                                        Distributor
                                    </span>
                                @endif
                            </div>

                            <span class="absolute top-3 right-3 inline-flex items-center gap-1 bg-white/95 backdrop-blur-xs text-neutral-600 text-[10px] font-semibold uppercase tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs z-10">
                                <i class="fa-solid fa-check text-[9px] text-[#0000FF]"></i> 100% Ori
                            </span>

                            <a href="{{ route('product.detail', $prod->slug) }}" class="absolute inset-0 z-0" aria-label="Lihat Detail {{ $prod->name }}"></a>
                        </div>

                        <!-- Product Content -->
                        <div class="p-5">
                            <div class="flex items-center justify-between text-[11px] font-bold tracking-wider uppercase text-neutral-400 mb-1.5">
                                <span>{{ $prod->brand ? $prod->brand->name : 'PasarKita' }}</span>
                                <span class="text-neutral-400 font-medium text-[10px] normal-case capitalize">{{ $prod->categories->first()?->name ?? 'Sneakers' }}</span>
                            </div>
                            
                            <h3 class="font-bold text-sm sm:text-[15px] text-neutral-900 group-hover:text-[#0000FF] transition-colors line-clamp-1 leading-snug tracking-tight">
                                <a href="{{ route('product.detail', $prod->slug) }}">{{ $prod->name }}</a>
                            </h3>

                            <!-- Rentang Harga & Colorway -->
                            <div class="mt-3.5 pt-3 border-t border-neutral-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[9px] uppercase tracking-wider font-semibold text-neutral-400 block">Harga</span>
                                    <span class="text-base font-extrabold text-neutral-950 tracking-tight">
                                        @if($prod->min_price == $prod->max_price)
                                            Rp {{ number_format($prod->min_price, 0, ',', '.') }}
                                        @else
                                            Rp {{ number_format($prod->min_price, 0, ',', '.') }} – Rp {{ number_format($prod->max_price, 0, ',', '.') }}
                                        @endif
                                    </span>
                                </div>

                                <!-- Varian Warna Preview -->
                                @if($prod->colors->count() > 0)
                                    <div class="flex items-center gap-1.5 pb-0.5">
                                        @foreach($prod->colors->take(3) as $col)
                                            <span class="w-3.5 h-3.5 rounded-full border border-neutral-200 shadow-2xs" style="background-color: {{ $col->hex_code }}" title="{{ $col->color_name }}"></span>
                                        @endforeach
                                        @if($prod->colors->count() > 3)
                                            <span class="text-[10px] font-bold text-neutral-400">+{{ $prod->colors->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- CTA Detail Button (Adem, Soft, Tactile) -->
                    <div class="p-5 pt-0">
                        <a href="{{ route('product.detail', $prod->slug) }}" 
                           class="group/btn w-full text-center py-2.5 sm:py-3 bg-neutral-50 hover:bg-[#0000FF] text-neutral-700 hover:text-white border border-neutral-200/90 hover:border-[#0000FF] rounded-xl active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 text-xs font-semibold tracking-wide shadow-2xs hover:shadow-sm">
                            <span>Pilih Ukuran & Beli</span>
                            <i class="fa-solid fa-arrow-right text-[10px] text-neutral-400 group-hover/btn:text-white group-hover/btn:translate-x-0.5 transition-all"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Tombol Buka Katalog Adem & Soft -->
        <div class="text-center mt-12">
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 bg-neutral-50 hover:bg-[#0000FF] text-neutral-700 hover:text-white border border-neutral-300 hover:border-[#0000FF] px-8 py-3.5 text-xs uppercase tracking-wider font-semibold rounded-xl shadow-2xs hover:shadow-sm active:scale-[0.98] transition-all cursor-pointer">
                <span>Buka Seluruh Katalog Sepatu</span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
        </div>
    </div>
</section>

<!-- 5. 4 Pilar Keunggulan Toko -->
<section class="py-16 border-b border-neutral-200 bg-neutral-50/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($features as $feat)
                <div class="space-y-3 bg-white p-6 border border-neutral-200/90 rounded-2xl shadow-2xs">
                    <span class="text-xs font-mono font-bold text-neutral-400">0{{ $loop->iteration }}</span>
                    <h3 class="text-sm font-bold text-neutral-950 uppercase tracking-wide">{{ $feat->title }}</h3>
                    <p class="text-xs text-neutral-500 leading-relaxed font-normal">{{ $feat->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 6. Banner Akses Member & Kemitraan (B2B / Pre-Order) -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="border border-neutral-200 p-8 sm:p-14 flex flex-col md:flex-row items-center justify-between gap-8 bg-neutral-50/40 rounded-3xl">
            <div class="max-w-xl space-y-4">
                <span class="text-[10px] uppercase tracking-widest font-bold text-neutral-400">Tier Akses Eksklusif</span>
                <h2 class="text-3xl font-extrabold text-neutral-950 tracking-tight">Bergabung Menjadi Member Sneakerhead atau Reseller PasarKita</h2>
                <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed font-normal">
                    Dapatkan akses prioritas ke seluruh katalog Pre-Order (PO) sepatu langka, pemanfaatan kupon diskon member, aturan potongan harga grosir otomatis untuk reseller partai, serta keamanan double-box packing terpercaya.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                <a href="{{ route('register') }}" class="bg-[#0000FF] hover:bg-blue-700 text-white text-xs uppercase tracking-wider font-semibold px-8 py-3.5 rounded-xl text-center transition-all duration-200 shadow-2xs hover:shadow-xs active:scale-[0.98]">
                    Daftar Member Gratis
                </a>
                <a href="{{ route('login') }}" class="border border-neutral-300 bg-white text-neutral-800 text-xs uppercase tracking-wider font-semibold px-8 py-3.5 rounded-xl text-center hover:border-neutral-950 hover:bg-neutral-50 transition-all duration-200 active:scale-[0.98]">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
