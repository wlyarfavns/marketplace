@extends('layouts.app')

@section('content')
<!-- 1. Hero Section Editorial Clean Minimalist (International High-End Aesthetic) -->
<section class="border-b border-neutral-200/80 bg-gradient-to-b from-neutral-50/60 via-white to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-14 lg:py-20 flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-16">
        
        <!-- Left: Hero Headline & Description -->
        <div class="w-full lg:w-1/2 space-y-6 text-left">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white border border-neutral-200/90 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-neutral-950 animate-pulse"></span>
                <span class="text-[11px] uppercase tracking-wider font-bold text-neutral-700">Archived & Modern Footwear Curation</span>
            </div>
            
            <h1 class="text-4xl sm:text-5xl lg:text-[54px] font-extrabold text-neutral-950 leading-[1.08] tracking-tight">
                Koleksi Sepatu Branded Original & Terkurasi.
            </h1>
            
            <p class="text-sm sm:text-base text-neutral-600 leading-relaxed font-normal max-w-xl">
                Destinasi kurasi sneakers resmi bagi pencinta siluet autentik dari Nike, Air Jordan, New Balance, Asics, hingga Adidas. Menjamin 100% keaslian produk, proteksi ekstra double-box, serta alokasi Pre-Order resmi.
            </p>
            
            <div class="flex flex-wrap items-center gap-3.5 pt-2">
                <a href="{{ route('catalog', ['type' => 'ready_stock']) }}" 
                   class="bg-neutral-950 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold px-7 py-3.5 rounded-full transition-all duration-200 shadow-xs hover:shadow-md active:scale-[0.98] cursor-pointer inline-flex items-center gap-2.5">
                    <span>Jelajahi Ready Stock</span>
                    <i class="fa-solid fa-arrow-right text-[11px]"></i>
                </a>
                <a href="{{ route('catalog') }}" 
                   class="border border-neutral-300 bg-white hover:border-neutral-950 hover:text-neutral-950 text-neutral-800 text-xs uppercase tracking-wider font-semibold px-7 py-3.5 rounded-full transition-all duration-200 shadow-2xs hover:shadow-2xs active:scale-[0.98] cursor-pointer">
                    Semua Koleksi
                </a>
            </div>

            <!-- Mini Trust Badges -->
            <div class="pt-6 border-t border-neutral-200/70 flex flex-wrap items-center gap-6 text-xs text-neutral-600">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-check text-neutral-950 text-sm"></i>
                    <span class="font-medium">100% Verified Authentic</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-box text-neutral-950 text-sm"></i>
                    <span class="font-medium">Secured Double-Box</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-truck-fast text-neutral-950 text-sm"></i>
                    <span class="font-medium">Pengiriman Seluruh Nusantara</span>
                </div>
            </div>
        </div>

        <!-- Right: Interactive Editorial Showcase (High-End Tactile Card) -->
        <div class="w-full lg:w-5/12" x-data="{
            activeIndex: 0,
            shoes: [
                {
                    brand: 'ASICS',
                    name: 'Asics GEL-Kayano 14',
                    subtitle: 'Metallic Silver & Black Edition',
                    price: 'Rp 2.299.000',
                    tag: 'Performance & Style',
                    slug: 'asics-gel-kayano-14-metallic-silver-black',
                    image: '{{ asset('images/shoes/asics-gel-kayano.jpg') }}'
                },
                {
                    brand: 'NEW BALANCE',
                    name: 'New Balance 610v1',
                    subtitle: 'All-Terrain Trail Runner',
                    price: 'Rp 1.899.000',
                    tag: 'Gorpcore Outdoor',
                    slug: 'new-balance-610v1-trail-runner',
                    image: '{{ asset('images/shoes/nb-610-trail.jpg') }}'
                },
                {
                    brand: 'ADIDAS',
                    name: 'Adidas Samba OG',
                    subtitle: 'Cloud White & Core Black Gum',
                    price: 'Rp 1.899.000',
                    tag: 'Terrace Classic',
                    slug: 'adidas-samba-og-cloud-white-core-black',
                    image: '{{ asset('images/shoes/adidas-samba-white.jpg') }}'
                },
                {
                    brand: 'VANS',
                    name: 'Vans UltraRange EXO',
                    subtitle: 'MTE All-Weather Trail',
                    price: 'Rp 1.799.000',
                    tag: 'All-Terrain Comfort',
                    slug: 'vans-ultrarange-exo-mte-trail',
                    image: '{{ asset('images/shoes/vans-ultrarange-trail.jpg') }}'
                }
            ]
        }">
            <div class="relative bg-white border border-neutral-200/90 rounded-3xl p-6 sm:p-7 shadow-[0_10px_35px_rgba(0,0,0,0.04)] hover:shadow-[0_15px_45px_rgba(0,0,0,0.07)] transition-all duration-300">
                
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-neutral-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-neutral-900"></span>
                        <span class="text-[10px] tracking-widest uppercase font-bold text-neutral-400" x-text="shoes[activeIndex].brand"></span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-neutral-100 text-neutral-700" x-text="shoes[activeIndex].tag"></span>
                </div>

                <!-- Product Display Image Box -->
                <div class="relative aspect-[4/3] bg-gradient-to-b from-[#FBFBFB] to-[#F5F5F5] rounded-2xl flex items-center justify-center p-6 overflow-hidden border border-neutral-100 group">
                    <img :src="shoes[activeIndex].image" 
                         :alt="shoes[activeIndex].name" 
                         class="w-full h-full object-contain filter drop-shadow-sm group-hover:scale-105 transition-all duration-500">
                    
                    <span class="absolute top-3 left-3 bg-white/95 backdrop-blur-xs text-neutral-800 text-[10px] uppercase tracking-wider font-bold px-2.5 py-1 rounded-md border border-neutral-200/90 shadow-2xs">
                        Pilihan Editor
                    </span>
                </div>

                <!-- Product Info & Actions -->
                <div class="mt-5 space-y-3">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-neutral-950 tracking-tight" x-text="shoes[activeIndex].name"></h3>
                            <p class="text-xs text-neutral-500 font-normal mt-0.5" x-text="shoes[activeIndex].subtitle"></p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-neutral-400 block">Harga</span>
                            <span class="text-base font-extrabold text-neutral-950" x-text="shoes[activeIndex].price"></span>
                        </div>
                    </div>

                    <!-- Interactive Thumbnail Switcher -->
                    <div class="pt-3 border-t border-neutral-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-neutral-400">Siluet Populer Lainnya</span>
                            <span class="text-[10px] font-semibold text-neutral-500" x-text="(activeIndex + 1) + ' / ' + shoes.length"></span>
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="(s, index) in shoes" :key="index">
                                <button type="button" 
                                        @click="activeIndex = index" 
                                        :class="activeIndex === index ? 'border-neutral-950 ring-2 ring-neutral-950/20 bg-neutral-100' : 'border-neutral-200 bg-[#FAFAFA] hover:border-neutral-400'"
                                        class="aspect-square rounded-xl border p-1.5 transition-all duration-150 cursor-pointer flex items-center justify-center overflow-hidden">
                                    <img :src="s.image" :alt="s.name" class="w-full h-full object-contain">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- View Detail Button -->
                    <div class="pt-2">
                        <a :href="'/produk/' + shoes[activeIndex].slug" 
                           class="w-full py-3 bg-neutral-950 hover:bg-neutral-800 text-white rounded-xl text-xs uppercase tracking-wider font-bold transition-all flex items-center justify-center gap-2 cursor-pointer shadow-2xs hover:shadow-sm active:scale-[0.98]">
                            <span>Lihat Detail Sepatu</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- 2. Minimal Runway Brand Ribbon (Monochrome High-End Strip) -->
<section class="py-6 border-b border-neutral-200/80 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="flex items-center justify-between gap-6 sm:gap-10 overflow-x-auto no-scrollbar py-2 text-xs font-extrabold tracking-[0.25em] text-neutral-400 uppercase">
            @foreach($brands as $b)
                <a href="{{ route('catalog', ['brand' => $b->slug]) }}" 
                   class="hover:text-neutral-950 transition-colors whitespace-nowrap">
                    {{ $b->name }}
                </a>
                @if(!$loop->last)
                    <span class="text-neutral-200 select-none">&bull;</span>
                @endif
            @endforeach
        </div>
    </div>
</section>

<!-- 3. Koleksi Produk Pilihan (Sleek International Grid Layout) -->
<section class="py-16 sm:py-20 border-b border-neutral-200/80 bg-neutral-50/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        
        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-10 sm:mb-12 gap-4">
            <div>
                <span class="text-[11px] uppercase tracking-widest font-bold text-neutral-400">Curated Drops</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight mt-1">Koleksi Sepatu Branded Pilihan</h2>
            </div>
            
            <div class="flex items-center gap-2 text-xs">
                <a href="{{ route('catalog', ['type' => 'ready_stock']) }}" 
                   class="px-4 py-2 rounded-full border border-neutral-200 bg-white hover:border-neutral-950 transition font-semibold text-neutral-800 shadow-2xs">
                    Ready Stock
                </a>
                @auth
                    @if(in_array(Auth::user()->customer_type, ['member', 'distributor']))
                        <a href="{{ route('catalog', ['type' => 'po']) }}" 
                           class="px-4 py-2 rounded-full border border-neutral-200 bg-white hover:border-neutral-950 transition font-semibold text-neutral-800 shadow-2xs">
                            Pre-Order (PO)
                        </a>
                    @endif
                @endauth
                <a href="{{ route('catalog') }}" 
                   class="px-4 py-2 rounded-full border border-neutral-200 bg-white hover:border-neutral-950 transition font-semibold text-neutral-800 shadow-2xs">
                    Semua
                </a>
            </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7">
            @foreach($featuredProducts as $prod)
                <div class="group relative flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white hover:border-neutral-400 hover:shadow-[0_12px_30px_rgba(0,0,0,0.06)] transition-all duration-300 overflow-hidden">
                    <div>
                        <!-- Thumbnail Box (Pure Studio Soft Background) -->
                        <div class="relative aspect-square bg-[#F7F7F7] p-5 flex items-center justify-center overflow-hidden border-b border-neutral-100">
                            @if($prod->primary_image_url)
                                <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" 
                                     onerror="if (this.src !== '{{ $prod->primary_image_url }}') this.src = '{{ $prod->primary_image_url }}';"
                                     class="w-full h-full object-contain group-hover:scale-108 group-hover:-translate-y-1 transition-all duration-500 filter drop-shadow-xs">
                            @else
                                <i class="fa-solid fa-shoe-prints text-neutral-300 text-4xl group-hover:scale-110 transition duration-300"></i>
                            @endif
                            
                            <!-- Badges (Soft & Elegant) -->
                            <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                                @if($prod->product_type === 'ready_stock')
                                    <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs text-neutral-800 text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-neutral-950"></span>
                                        Ready Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-xs text-neutral-800 text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs">
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

                            <span class="absolute top-3 right-3 inline-flex items-center gap-1 bg-white/95 backdrop-blur-xs text-neutral-700 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md border border-neutral-200/80 shadow-2xs z-10">
                                <i class="fa-solid fa-check text-[9px] text-neutral-950"></i> 100% Ori
                            </span>

                            <a href="{{ route('product.detail', $prod->slug) }}" class="absolute inset-0 z-0" aria-label="Lihat Detail {{ $prod->name }}"></a>
                        </div>

                        <!-- Product Content -->
                        <div class="p-5">
                            <div class="flex items-center justify-between text-[10px] font-bold tracking-widest uppercase text-neutral-400 mb-1.5">
                                <span>{{ $prod->brand ? $prod->brand->name : 'FOOTWORK' }}</span>
                                <span class="text-neutral-400 font-medium normal-case capitalize">{{ $prod->categories->first()?->name ?? 'Sneakers' }}</span>
                            </div>
                            
                            <h3 class="font-bold text-sm sm:text-[15px] text-neutral-900 group-hover:text-black transition-colors line-clamp-1 leading-snug tracking-tight">
                                <a href="{{ route('product.detail', $prod->slug) }}">{{ $prod->name }}</a>
                            </h3>

                            <!-- Price & Swatches -->
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

                                <!-- Colorway Swatches -->
                                @if($prod->colors->count() > 0)
                                    <div class="flex items-center gap-1.5 pb-0.5">
                                        @foreach($prod->colors->take(3) as $col)
                                            <span class="w-3.5 h-3.5 rounded-full border border-neutral-300 shadow-2xs" style="background-color: {{ $col->hex_code }}" title="{{ $col->color_name }}"></span>
                                        @endforeach
                                        @if($prod->colors->count() > 3)
                                            <span class="text-[10px] font-bold text-neutral-400">+{{ $prod->colors->count() - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="p-5 pt-0">
                        <a href="{{ route('product.detail', $prod->slug) }}" 
                           class="w-full text-center py-2.5 sm:py-3 bg-neutral-50 hover:bg-neutral-950 text-neutral-800 hover:text-white border border-neutral-200/90 hover:border-neutral-950 rounded-xl active:scale-[0.98] transition-all duration-200 cursor-pointer flex items-center justify-center gap-2 text-xs font-semibold tracking-wide shadow-2xs hover:shadow-xs">
                            <span>Pilih Ukuran & Beli</span>
                            <i class="fa-solid fa-arrow-right text-[10px] text-neutral-400 group-hover:text-white transition-all"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Browse All CTA -->
        <div class="text-center mt-12">
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2.5 bg-white hover:bg-neutral-950 text-neutral-900 hover:text-white border border-neutral-300 hover:border-neutral-950 px-8 py-3.5 text-xs uppercase tracking-wider font-bold rounded-full shadow-2xs hover:shadow-sm active:scale-[0.98] transition-all cursor-pointer">
                <span>Buka Seluruh Katalog Sepatu</span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
        </div>
    </div>
</section>

<!-- 4. Grid Brand Sepatu Pilihan (Editorial Brand Showcase) -->
<section class="py-16 sm:py-20 border-b border-neutral-200/80 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-10 gap-4">
            <div>
                <span class="text-[11px] uppercase tracking-widest font-bold text-neutral-400">Brand Resmi Terdaftar</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight mt-1">Brand Pilihan di FOOTWORK</h2>
            </div>
            <a href="{{ route('catalog') }}" class="text-xs uppercase tracking-wider font-semibold border-b border-neutral-300 pb-0.5 text-neutral-700 hover:text-neutral-950 hover:border-neutral-950 transition">
                Lihat Seluruh Brand &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 sm:gap-6">
            @foreach($brands as $b)
                <a href="{{ route('catalog', ['brand' => $b->slug]) }}" 
                   class="group bg-neutral-50/50 hover:bg-white p-6 rounded-2xl border border-neutral-200/80 hover:border-neutral-950 hover:shadow-md transition-all duration-200 flex flex-col justify-between h-44 shadow-2xs">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-neutral-400 font-bold block mb-1">Brand Mitra #{{ $loop->iteration }}</span>
                        <h3 class="text-lg font-extrabold text-neutral-950 group-hover:text-black transition-colors tracking-tight">{{ $b->name }}</h3>
                        <p class="text-xs text-neutral-500 line-clamp-2 mt-2 leading-relaxed font-normal">{{ $b->description }}</p>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-neutral-200/60 text-[11px] text-neutral-600 font-medium">
                        <span>DP Mulai {{ number_format($b->dp_percentage, 0) }}%</span>
                        <i class="fa-solid fa-arrow-right text-neutral-400 group-hover:text-neutral-950 group-hover:translate-x-1 transition-all"></i>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- 5. Banner Promo Eksklusif (Clean Soft Card) -->
@if($banners->count() > 0)
<section class="py-14 border-b border-neutral-200/80 bg-neutral-50/30">
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
                    <span class="inline-block text-[10px] tracking-widest uppercase font-bold text-neutral-700 bg-neutral-100 px-3 py-1 rounded-full border border-neutral-200/80">
                        Promo Eksklusif FOOTWORK
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-neutral-950 leading-tight tracking-tight">
                        {{ $banner->title }}
                    </h2>
                    <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed font-normal max-w-md">
                        {{ $banner->description }}
                    </p>
                    @if($banner->url)
                        <div class="pt-1">
                            <a href="{{ $banner->url }}" class="inline-flex items-center gap-2 bg-neutral-950 hover:bg-neutral-800 text-white border border-neutral-950 text-xs uppercase tracking-wider font-bold px-7 py-3 rounded-full transition-all duration-200 shadow-2xs hover:shadow-sm active:scale-[0.98] cursor-pointer">
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

<!-- 6. 4 Pilar Keunggulan Toko (Minimalist Clean Columns) -->
<section class="py-16 sm:py-20 border-b border-neutral-200/80 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="mb-10 text-center max-w-xl mx-auto space-y-1">
            <span class="text-[11px] uppercase tracking-widest font-bold text-neutral-400">Standar Kualitas</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">Komitmen Kami untuk Sneakerhead</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($features as $feat)
                <div class="space-y-3 bg-neutral-50/50 p-6 sm:p-7 border border-neutral-200/80 rounded-2xl shadow-2xs hover:border-neutral-400 transition-all">
                    <span class="text-xs font-mono font-bold text-neutral-400">0{{ $loop->iteration }}</span>
                    <h3 class="text-sm font-bold text-neutral-950 uppercase tracking-wide">{{ $feat->title }}</h3>
                    <p class="text-xs text-neutral-500 leading-relaxed font-normal">{{ $feat->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- 7. Banner Akses Member & Kemitraan (B2B / Pre-Order) -->
<section class="py-16 sm:py-20 bg-neutral-50/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <div class="border border-neutral-200/90 p-8 sm:p-14 flex flex-col md:flex-row items-center justify-between gap-8 bg-white rounded-3xl shadow-xs">
            <div class="max-w-xl space-y-3">
                <span class="text-[10px] uppercase tracking-widest font-bold text-neutral-400">Tier Akses Eksklusif</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 tracking-tight">Bergabung Menjadi Member Sneakerhead atau Reseller FOOTWORK</h2>
                <p class="text-xs sm:text-sm text-neutral-600 leading-relaxed font-normal">
                    Dapatkan akses prioritas ke seluruh katalog Pre-Order (PO) sepatu langka, pemanfaatan kupon diskon member, aturan potongan harga grosir otomatis untuk reseller partai, serta keamanan double-box packing terpercaya.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                <a href="{{ route('register') }}" class="bg-neutral-950 hover:bg-neutral-800 text-white text-xs uppercase tracking-wider font-bold px-8 py-3.5 rounded-full text-center transition-all duration-200 shadow-2xs hover:shadow-xs active:scale-[0.98]">
                    Daftar Member Gratis
                </a>
                <a href="{{ route('login') }}" class="border border-neutral-300 bg-white text-neutral-800 hover:text-neutral-950 text-xs uppercase tracking-wider font-semibold px-8 py-3.5 rounded-full text-center hover:border-neutral-950 hover:bg-neutral-50 transition-all duration-200 active:scale-[0.98]">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
