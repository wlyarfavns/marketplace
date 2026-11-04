@extends('layouts.admin')

@section('page_title', '5. Manajemen Brand Mitra')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Brand Sepatu Branded</h2>
            <p class="text-xs text-slate-500 mt-0.5">Atur nama brand sepatu, aturan persentase Down Payment (DP%), dan urutan tampil di Beranda.</p>
        </div>
        <button @click="addModal = true" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Tambah Brand Baru
        </button>
    </div>

    <!-- Tabel Brand -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">Urutan Home</th>
                    <th class="p-4">Nama Brand</th>
                    <th class="p-4">Persentase DP (%)</th>
                    <th class="p-4">Total Produk</th>
                    <th class="p-4 text-center">Tampil di Home</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($brands as $b)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4 font-bold text-slate-400">#{{ $b->homepage_order }}</td>
                        <td class="p-4">
                            <span class="font-bold text-slate-900 text-sm block">{{ $b->name }}</span>
                            <span class="text-slate-400 text-[11px]">{{ $b->description }}</span>
                        </td>
                        <td class="p-4">
                            <span class="bg-slate-900 text-white font-bold px-2 py-0.5 text-xs">
                                {{ number_format($b->dp_percentage, 0) }}%
                            </span>
                        </td>
                        <td class="p-4 font-bold text-slate-700">{{ $b->products_count }} Produk</td>
                        <td class="p-4 text-center">
                            @if($b->show_on_homepage)
                                <span class="text-emerald-600 font-bold"><i class="fa-solid fa-check"></i> Ya</span>
                            @else
                                <span class="text-slate-300">Tidak</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold {{ $b->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $b->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.brands.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus brand ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada brand terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $brands->links() }}</div>

    <!-- Modal Tambah Brand -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Brand Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.brands.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Brand *</label>
                    <input type="text" name="name" required placeholder="Contoh: Nike, Jordan, Adidas" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Persentase DP (%) (Default 30.00%) *</label>
                    <input type="number" name="dp_percentage" required step="0.01" value="30.00" min="0" max="100" class="w-full px-3 py-2 border border-slate-300 font-bold">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Deskripsi Singkat</label>
                    <input type="text" name="description" placeholder="Spesialisasi sneaker & sepatu..." class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nomor Urutan di Beranda</label>
                    <input type="number" name="homepage_order" value="1" min="0" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="show_on_homepage" value="1" checked class="text-black focus:ring-0">
                        <span class="font-medium text-slate-700">Tampilkan di Carousel Beranda</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Simpan Brand</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

