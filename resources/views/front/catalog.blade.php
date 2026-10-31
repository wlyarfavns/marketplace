@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">
        <span class="text-xs uppercase tracking-widest text-black font-bold">Katalog Sepatu Branded</span>
        <h1 class="text-3xl font-serif font-bold text-gray-950 mt-1">Koleksi PasarKita</h1>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-10">
        
        <!-- Filter Sidebar -->
        <aside class="w-full lg:w-64 shrink-0 space-y-8">
            <form action="{{ route('catalog') }}" method="GET" id="filterForm">
                
                <!-- Search Input -->
                <div class="space-y-3 pb-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Pencarian Sepatu</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari model, SKU, atau brand..." 
                            class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black bg-white shadow-2xs">
                    </div>
                </div>

                <!-- Jenis Produk (Ready Stock vs PO) -->
                <div class="space-y-3 py-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Tipe Produk</label>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                            <input type="radio" name="type" value="" {{ !request('type') ? 'checked' : '' }} onchange="this.form.submit()" class="text-black focus:ring-0">
                            <span class="font-medium">Semua Sepatu</span>
                        </label>
                        <label class="flex items-center gap-2.5 cursor-pointer select-none">
                            <input type="radio" name="type" value="ready_stock" {{ request('type') === 'ready_stock' ? 'checked' : '' }} onchange="this.form.submit()" class="text-black focus:ring-0">
                            <span class="font-medium">Stok Siap Kirim (Ready Stock)</span>
                        </label>

                        @if($userType !== 'umum')
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="radio" name="type" value="po" {{ request('type') === 'po' ? 'checked' : '' }} onchange="this.form.submit()" class="text-black focus:ring-0">
                                <span class="font-bold text-gray-900">Prapesan (Pre-Order)</span>
                                <span class="bg-black text-[9px] font-bold px-1.5 py-0.5 rounded text-white">MEMBER</span>
                            </label>
                        @else
                            <div class="pt-1 text-[11px] text-gray-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-lock text-[10px]"></i>
                                <span>Prapesan khusus <a href="{{ route('login') }}" class="underline text-black font-bold">Member & Mitra</a></span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="space-y-3 py-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Brand Sepatu</label>
                    <select name="brand" onchange="this.form.submit()" class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded bg-white focus:outline-hidden focus:border-black cursor-pointer font-medium shadow-2xs">
                        <option value="">Semua Brand</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->slug }}" {{ request('brand') === $b->slug ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori Filter -->
                <div class="space-y-3 py-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Kategori Sepatu</label>
                    <select name="category" onchange="this.form.submit()" class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded bg-white focus:outline-hidden focus:border-black cursor-pointer font-medium shadow-2xs">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->slug }}" {{ request('category') === $c->slug ? 'selected' : '' }}>
                                {{ $c->name }} ({{ ucfirst($c->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Urutkan -->
                <div class="space-y-3 py-6 border-b border-gray-200">
                    <label class="text-xs uppercase font-bold text-gray-900 tracking-wider">Urutkan</label>
                    <select name="sort" onchange="this.form.submit()" class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded bg-white focus:outline-hidden focus:border-black cursor-pointer font-medium shadow-2xs">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                    </select>
                </div>

                <div class="pt-4 flex gap-2">
                    <button type="submit" class="flex-1 bg-black text-white text-xs uppercase tracking-wider py-3 font-bold hover:bg-neutral-800 transition shadow-sm rounded border-2 border-black active:translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-sliders text-xs"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    @if(request()->anyFilled(['q', 'brand', 'category', 'type', 'sort']))
                        <a href="{{ route('catalog') }}" class="px-4 py-3 border-2 border-gray-300 bg-white text-xs font-bold text-gray-700 hover:text-black hover:border-black transition rounded shadow-2xs active:translate-y-0.5 cursor-pointer flex items-center gap-1.5" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>
        </aside>

        <!-- Product Grid -->
        <div class="flex-1">
            <div class="flex justify-between items-center mb-6 text-xs text-gray-500">
                <p>Menampilkan <span class="font-bold text-gray-900">{{ $products->total() }}</span> produk sepatu</p>
                <div class="text-gray-400">
                    Status Akses: <span class="uppercase font-bold text-black">{{ $userType === 'umum' ? 'Pengunjung Umum' : ($userType === 'member' ? 'Member Resmi' : 'Mitra Distributor') }}</span>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20 border-2 border-dashed border-gray-200 bg-gray-50 rounded-lg p-8">
                    <i class="fa-solid fa-shoe-prints text-4xl text-gray-300 mb-4 block"></i>
                    <p class="text-base font-bold text-gray-800">Tidak ada koleksi sepatu yang sesuai dengan filter.</p>
                    <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">Coba gunakan kata kunci pencarian lain, pilih brand berbeda, atau reset filter untuk melihat seluruh koleksi sepatu.</p>
                    <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 mt-5 px-6 py-3 bg-black text-white text-xs font-bold uppercase tracking-wider rounded border-2 border-black hover:bg-neutral-800 shadow-sm active:translate-y-0.5 transition cursor-pointer">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Reset Filter Pencarian</span>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($products as $prod)
                        <div class="group flex flex-col justify-between border-2 border-gray-200 bg-white hover:border-black transition duration-200 rounded-lg overflow-hidden shadow-2xs hover:shadow-md">
                            <div>
                                <div class="relative aspect-[3/4] bg-neutral-100 flex items-center justify-center overflow-hidden border-b border-gray-100">
                                    @if($prod->primary_image_url)
                                        <img src="{{ $prod->primary_image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <i class="fa-solid fa-shoe-prints text-gray-300 text-4xl group-hover:scale-110 transition duration-300"></i>
                                    @endif
                                    
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                                        @if($prod->product_type === 'ready_stock')
                                            <span class="bg-black text-white text-[9px] uppercase tracking-wider font-bold px-2.5 py-1 rounded shadow-xs">
                                                Stok Siap Kirim
                                            </span>
                                        @else
                                            <span class="bg-black text-white text-[9px] uppercase tracking-wider font-bold px-2.5 py-1 rounded shadow-xs">
                                                Prapesan (PO)
                                            </span>
                                        @endif

                                        @if($prod->visible_to_distributor && !$prod->visible_to_umum && !$prod->visible_to_member)
                                            <span class="bg-neutral-900 text-white text-[9px] uppercase tracking-wider font-bold px-2 py-0.5 border border-neutral-700 rounded">
                                                Khusus Distributor (B2B)
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('product.detail', $prod->slug) }}" class="absolute inset-0 z-10" aria-label="Lihat Detail Produk {{ $prod->name }}"></a>
                                </div>

                                <div class="p-5">
                                    <span class="text-[10px] uppercase tracking-wider text-gray-500 font-bold block mb-1">
                                        {{ $prod->brand ? $prod->brand->name : 'PasarKita' }}
                                    </span>
                                    <h3 class="font-serif text-base font-bold text-gray-900 group-hover:underline line-clamp-1">
                                        <a href="{{ route('product.detail', $prod->slug) }}">{{ $prod->name }}</a>
                                    </h3>

                                    <div class="mt-3">
                                        <span class="text-sm font-bold text-gray-950">
                                            @if($prod->min_price == $prod->max_price)
                                                Rp {{ number_format($prod->min_price, 0, ',', '.') }}
                                            @else
                                                Rp {{ number_format($prod->min_price, 0, ',', '.') }} – Rp {{ number_format($prod->max_price, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </div>

                                    @if($prod->colors->count() > 0)
                                        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-gray-100">
                                            @foreach($prod->colors->take(4) as $col)
                                                <span class="w-3.5 h-3.5 rounded-full border border-gray-300" style="background-color: {{ $col->hex_code }}" title="{{ $col->color_name }}"></span>
                                            @endforeach
                                            @if($prod->colors->count() > 4)
                                                <span class="text-[10px] text-gray-400 font-semibold">+{{ $prod->colors->count() - 4 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="p-5 pt-0">
                                <a href="{{ route('product.detail', $prod->slug) }}" 
                                   class="w-full text-center block py-2.5 border-2 border-black bg-white text-xs uppercase tracking-wider font-bold text-black hover:bg-black hover:text-white transition shadow-xs rounded active:translate-y-0.5 cursor-pointer">
                                    Pilih Ukuran & Beli
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
