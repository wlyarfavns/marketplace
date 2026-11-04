@extends('layouts.admin')

@section('page_title', '4. Manajemen Produk & Matriks Harga')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Katalog Produk Multi-Brand</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh produk, matriks varian model &times; ukuran, serta aturan bonus barang.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Tambah Produk Baru
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200 p-4">
        <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama Produk / SKU..." 
                       class="w-full px-3 py-2 border border-slate-300 focus:outline-none focus:border-slate-800">
            </div>

            <div>
                <select name="brand_id" class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                    <option value="">Semua Brand</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="product_type" class="w-full px-3 py-2 border border-slate-300 bg-white focus:outline-none focus:border-slate-800">
                    <option value="">Semua Tipe Stok</option>
                    <option value="ready_stock" {{ request('product_type') === 'ready_stock' ? 'selected' : '' }}>Ready Stock</option>
                    <option value="po" {{ request('product_type') === 'po' ? 'selected' : '' }}>Pre-Order (PO)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="w-full bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-800 font-semibold py-2 transition">
                    Filter
                </button>
                @if(request()->anyFilled(['q', 'brand_id', 'product_type']))
                    <a href="{{ route('admin.products.index') }}" class="px-3 py-2 border border-slate-300 text-slate-500 hover:text-black">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Produk -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">SKU / Nama Produk</th>
                    <th class="p-4">Brand</th>
                    <th class="p-4">Tipe Stok</th>
                    <th class="p-4">Katalog Pelanggan</th>
                    <th class="p-4 text-right">Rentang Harga</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products as $p)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4">
                            <span class="font-mono text-[11px] font-bold text-slate-500">{{ $p->sku }}</span>
                            <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $p->name }}</p>
                            <span class="text-[10px] text-slate-400">{{ $p->weight_grams }} gram &bull; {{ $p->variantPrices->count() }} varian matriks</span>
                        </td>
                        <td class="p-4 font-semibold text-slate-700">
                            {{ $p->brand ? $p->brand->name : '-' }}
                        </td>
                        <td class="p-4">
                            @if($p->product_type === 'ready_stock')
                                <span class="bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 border border-emerald-200 uppercase text-[10px]">Ready</span>
                            @else
                                <span class="bg-slate-900 text-white font-semibold px-2 py-0.5 uppercase text-[10px]">Pre-Order</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($p->visible_to_umum || $p->visible_to_member)
                                <span class="bg-slate-100 text-slate-800 font-semibold px-2 py-0.5 border border-slate-200 text-[10px]">Tampil di Toko</span>
                            @else
                                <span class="text-slate-400 border border-slate-200 px-2 py-0.5 text-[10px]">Disembunyikan</span>
                            @endif
                        </td>
                        <td class="p-4 text-right font-bold text-slate-900">
                            @if($p->min_price == $p->max_price)
                                Rp {{ number_format($p->min_price, 0, ',', '.') }}
                            @else
                                Rp {{ number_format($p->min_price, 0, ',', '.') }} – {{ number_format($p->max_price, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] uppercase font-semibold {{ $p->status === 'active' ? 'text-emerald-700 bg-emerald-50' : 'text-slate-400 bg-slate-100' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.products.edit', $p->id) }}" class="p-1.5 text-slate-400 hover:text-black" title="Edit Produk">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="{{ route('product.detail', $p->slug) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-black" title="Lihat di Toko">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada produk yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $products->links() }}
    </div>

</div>
@endsection

