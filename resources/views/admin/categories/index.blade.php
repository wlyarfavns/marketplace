@extends('layouts.admin')

@section('page_title', '6. Manajemen Kategori Sepatu')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Daftar Kategori Sepatu</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kategori sepatu branded dengan klasifikasi segmen Pria, Wanita, Unisex, dan Anak.</p>
        </div>
        <button @click="addModal = true" class="bg-black text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-black transition">
            + Tambah Kategori
        </button>
    </div>

    <!-- Tabel Kategori -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">Nama Kategori</th>
                    <th class="p-4">Tipe Segmen</th>
                    <th class="p-4">Deskripsi</th>
                    <th class="p-4 text-center">Jumlah Produk</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $c)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4 font-bold text-slate-900 text-sm">{{ $c->name }}</td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 font-bold uppercase text-[10px] 
                                {{ $c->type === 'wanita' ? 'bg-pink-50 text-pink-700 border border-pink-200' : ($c->type === 'pria' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700') }}">
                                {{ $c->type }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-500">{{ $c->description ?? '-' }}</td>
                        <td class="p-4 text-center font-bold text-slate-700">{{ $c->products_count }} Produk</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 text-[10px] uppercase font-bold {{ $c->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.categories.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
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
                        <td colspan="6" class="p-8 text-center text-slate-400">Belum ada kategori yang dibuat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Kategori -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Kategori Baru</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Kategori *</label>
                    <input type="text" name="name" required placeholder="Contoh: Sneakers & Lifestyle" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Tipe Segmen *</label>
                    <select name="type" required class="w-full px-3 py-2 border border-slate-300 bg-white">
                        <option value="unisex">Unisex (Sepatu Pria & Wanita)</option>
                        <option value="pria">Pria (Men's Shoes)</option>
                        <option value="wanita">Wanita (Women's Shoes)</option>
                        <option value="anak">Anak-Anak (Kids Shoes)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Deskripsi Kategori</label>
                    <textarea name="description" rows="2" placeholder="Keterangan kategori..." class="w-full px-3 py-2 border border-slate-300"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

