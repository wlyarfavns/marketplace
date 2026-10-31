@extends('layouts.app')

@section('content')
<!-- 1. Hero Section Editorial Clean Minimalist (Dominan Putih & Hitam) -->
<section class="border-b border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-20 lg:py-28 flex flex-col md:flex-row items-center justify-between gap-12">
        <div class="max-w-xl space-y-6">
            <span class="text-xs uppercase tracking-[0.25em] text-black font-black block">Branded Footwear & Sneakers Curation</span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-black text-black leading-tight">
                Koleksi Sepatu Branded Original & Terkurasi.
            </h1>
            <p class="text-sm sm:text-base text-gray-600 leading-relaxed font-light">
                Destinasi resmi sepatu branded dan sneakers original dari Nike, Air Jordan, Adidas, New Balance, Puma, dan Asics. Hadir dengan skema Ready Stock dan Pre-Order terpercaya dengan kemudahan Down Payment (DP).
            </p>
            <div class="flex flex-wrap items-center gap-4 pt-4">
                <a href="{{ route('catalog', ['type' => 'ready_stock']) }}" 
                   class="bg-black text-white text-xs uppercase tracking-widest font-bold px-8 py-4 hover:bg-gray-800 transition shadow-sm">
                    Jelajahi Ready Stock
                </a>
                <a href="{{ route('catalog') }}" 
                   class="border-2 border-black text-black text-xs uppercase tracking-widest font-bold px-8 py-4 hover:bg-black hover:text-white transition">
                    Semua Koleksi
                </a>
            </div>
        </div>

        <div class="w-full md:w-5/12">
            <div class="relative bg-white border-2 border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm hover:border-black hover:shadow-md transition duration-300 flex flex-col justify-between group">
                <div class="relative aspect-4/3 bg-gray-50 rounded-xl flex items-center justify-center p-4 overflow-hidden border border-gray-100">
                    <img src="{{ asset('images/shoes/nike-dunk-panda.jpg') }}" 
                         alt="Nike Dunk Low Retro Panda" 
                         class="w-full h-full object-contain group-hover:scale-105 transition duration-500">
                    <span class="absolute top-3 left-3 bg-black text-white text-[10px] uppercase tracking-wider font-bold px-2.5 py-1 rounded shadow-xs">
                        Pilihan Editor
                    </span>
                </div>
                <div class="mt-5 space-y-2 text-left">
                    <span class="text-[10px] tracking-widest uppercase font-bold text-gray-400">Sneakers Ikonik</span>
                    <h3 class="text-xl sm:text-2xl font-serif font-black text-gray-950">Nike Dunk Low Retro "Panda"</h3>
                    <p class="text-xs text-gray-500 leading-relaxed font-light">Siluet street-classic paling versatile dengan perpaduan warna kontras hitam dan putih ikonik.</p>
                    <div class="pt-3">
                        <a href="{{ route('product.detail', 'nike-dunk-low-retro-panda') }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-3 bg-black text-white text-xs uppercase tracking-widest font-black rounded-lg hover:bg-neutral-800 transition shadow-xs hover:shadow-md cursor-pointer">
                            <span>Lihat Detail Sepatu</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Carousel / Grid Brand Sepatu Pilihan -->
<section class="py-16 border-b border-gray-100 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
            <div>
                <span class="text-[11px] uppercase tracking-widest font-semibold text-gray-400">Brand Resmi Terdaftar</span>
                <h2 class="text-2xl font-serif font-black text-black mt-1">Brand Pilihan di PasarKita</h2>
            </div>
            <a href="{{ route('catalog') }}" class="text-xs uppercase tracking-wider font-bold border-b-2 border-black pb-0.5 text-black hover:text-gray-600 transition">
                Lihat Seluruh Brand &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($brands as $b)
                <a href="{{ route('catalog', ['brand' => $b->slug]) }}" 
                   class="group bg-white p-6 border border-gray-200 hover:border-black transition duration-200 flex flex-col justify-between h-44 shadow-xs">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold block mb-1">Brand Mitra #{{ $loop->iteration }}</span>
                        <h3 class="font-serif text-lg font-black text-black group-hover:underline">{{ $b->name }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2 mt-2 leading-relaxed">{{ $b->description }}</p>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] text-gray-600 font-semibold">
                        <span>DP Mulai {{ number_format($b->dp_percentage, 0) }}%</span>
                        <i class="fa-solid fa-arrow-right text-gray-400 group-hover:text-black transition"></i>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- 3. Banner Promo (Dominan Putih Bersih & Hitam Minimalis) -->
@if($banners->count() > 0)
<section class="py-14 border-b border-gray-200 bg-gray-50/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 space-y-6">
        @foreach($banners as $banner)
            <div class="relative w-full bg-white border border-gray-200 overflow-hidden flex items-center p-8 sm:p-12 md:p-14 shadow-xs hover:border-black transition duration-300 group min-h-[240px]">
                @if($banner->image_url)
                    <div class="absolute right-0 top-0 bottom-0 w-full sm:w-7/12 md:w-1/2 overflow-hidden">
                        <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700">
                        <!-- Gradien halus menyatu ke warna putih di sisi kiri -->
                        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent hidden sm:block"></div>
                        <!-- Overlay semi-transparan putih di layar mobile agar teks terbaca jelas -->
                        <div class="absolute inset-0 bg-white/90 sm:hidden"></div>
                    </div>
                @endif
                
                <div class="relative z-10 max-w-xl space-y-3.5">
                    <span class="inline-block text-[10px] tracking-widest uppercase font-bold text-gray-800 bg-gray-100 px-3 py-1 border border-gray-300">
                        Promo Eksklusif PasarKita
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-black text-black leading-tight">
                        {{ $banner->title }}
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal max-w-md">
                        {{ $banner->description }}
                    </p>
                    @if($banner->url)
                        <div class="pt-1">
                            <a href="{{ $banner->url }}" class="inline-block bg-black text-white text-xs uppercase tracking-widest font-bold px-7 py-3.5 hover:bg-neutral-800 transition shadow-xs">
                                Klaim Penawaran &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

<!-- 4. Koleksi Produk Pilihan (Rentang Harga Dinamis & Badge Hak Akses) -->
<section class="py-20 border-b border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-12 gap-4">
            <div>
                <span class="text-[11px] uppercase tracking-widest font-semibold text-gray-400">Koleksi Terkini</span>
                <h2 class="text-3xl font-serif font-black text-black mt-1">Koleksi Sepatu Branded Pilihan</h2>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <a href="{{ route('catalog', ['type' => 'ready_stock']) }}" class="px-4 py-2 border border-gray-200 hover:border-black transition font-semibold text-black">Ready Stock</a>
                @auth
                    @if(in_array(Auth::user()->customer_type, ['member', 'distributor']))
                        <a href="{{ route('catalog', ['type' => 'po']) }}" class="px-4 py-2 border border-black bg-black text-white transition font-semibold">Pre-Order (PO)</a>
                    @endif
                @endauth
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredProducts as $prod)
                <div class="group flex flex-col justify-between border border-gray-200 bg-white hover:border-black transition duration-200 shadow-xs">
                    <div>
                        <!-- Thumbnail Box -->
                        <div class="relative aspect-[3/4] bg-neutral-100 flex items-center justify-center overflow-hidden border-b border-gray-100">
                            @if($prod->primary_image_url)
                                <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <i class="fa-solid fa-shoe-prints text-gray-300 text-4xl group-hover:scale-110 transition duration-300"></i>
                            @endif
                            
                            <!-- Badges -->
                            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                @if($prod->product_type === 'ready_stock')
                                    <span class="bg-black text-white text-[9px] uppercase tracking-wider font-bold px-2 py-0.5">
                                        Ready Stock
                                    </span>
                                @else
                                    <span class="bg-gray-800 text-white text-[9px] uppercase tracking-wider font-bold px-2 py-0.5 border border-gray-700">
                                        Pre-Order (PO)
                                    </span>
                                @endif

                                @if($prod->visible_to_distributor && !$prod->visible_to_umum && !$prod->visible_to_member)
                                    <span class="bg-gray-900 text-white text-[9px] uppercase tracking-wider font-bold px-2 py-0.5 border border-gray-700">
                                        Khusus Distributor
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('product.detail', $prod->slug) }}" class="absolute inset-0 z-10" aria-label="Lihat Detail"></a>
                        </div>

                        <!-- Product Content -->
                        <div class="p-5">
                            <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold block mb-1">
                                {{ $prod->brand ? $prod->brand->name : 'PasarKita' }}
                            </span>
                            <h3 class="font-serif text-base font-bold text-black group-hover:underline line-clamp-1">
                                <a href="{{ route('product.detail', $prod->slug) }}">{{ $prod->name }}</a>
                            </h3>

                            <!-- Rentang Harga Dinamis -->
                            <div class="mt-3">
                                <span class="text-sm font-bold text-black">
                                    @if($prod->min_price == $prod->max_price)
                                        Rp {{ number_format($prod->min_price, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($prod->min_price, 0, ',', '.') }} – Rp {{ number_format($prod->max_price, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>

                            <!-- Varian Warna Preview -->
                            @if($prod->colors->count() > 0)
                                <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-gray-100">
                                    @foreach($prod->colors->take(4) as $col)
                                        <span class="w-3.5 h-3.5 rounded-full border border-gray-300" style="background-color: {{ $col->hex_code }}" title="{{ $col->color_name }}"></span>
                                    @endforeach
                                    @if($prod->colors->count() > 4)
                                        <span class="text-[10px] text-gray-400">+{{ $prod->colors->count() - 4 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- CTA Detail -->
                    <div class="p-5 pt-0">
                        <a href="{{ route('product.detail', $prod->slug) }}" 
                           class="w-full text-center block py-2.5 border-2 border-black text-xs uppercase tracking-wider font-bold text-black hover:bg-black hover:text-white transition">
                            Pilih Ukuran & Beli
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('catalog') }}" class="inline-block border-2 border-black text-black px-10 py-4 text-xs uppercase tracking-widest font-black hover:bg-black hover:text-white transition">
                Buka Seluruh Katalog Sepatu
            </a>
        </div>
    </div>
</section>

<!-- 5. 4 Pilar Keunggulan Toko -->
<section class="py-16 border-b border-gray-200 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @foreach($features as $feat)
                <div class="space-y-3 bg-white p-6 border border-gray-200">
                    <span class="text-xs font-serif font-black text-black">0{{ $loop->iteration }}</span>
                    <h3 class="text-sm font-black text-black uppercase tracking-wide">{{ $feat->title }}</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ $feat->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 6. Banner Akses Member & Kemitraan (B2B / Pre-Order) -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="border-2 border-black p-8 sm:p-16 flex flex-col md:flex-row items-center justify-between gap-8 bg-gray-50/50">
            <div class="max-w-xl space-y-4">
                <span class="text-[10px] uppercase tracking-widest font-black text-black">Tier Akses Eksklusif</span>
                <h2 class="text-3xl font-serif font-black text-black">Bergabung Menjadi Member Sneakerhead atau Reseller PasarKita</h2>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-light">
                    Dapatkan akses prioritas ke seluruh katalog Pre-Order (PO) sepatu langka, pemanfaatan kupon diskon member, aturan potongan harga grosir otomatis untuk reseller partai, serta keamanan double-box packing terpercaya.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 shrink-0">
                <a href="{{ route('register') }}" class="bg-black text-white text-xs uppercase tracking-widest font-bold px-8 py-4 text-center hover:bg-gray-800 transition">
                    Daftar Member Gratis
                </a>
                <a href="{{ route('login') }}" class="border-2 border-black text-black text-xs uppercase tracking-widest font-bold px-8 py-4 text-center hover:bg-white transition">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
