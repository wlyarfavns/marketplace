@extends('layouts.admin')

@section('page_title', '11. Pilar Keunggulan Toko')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">4 Pilar Nilai Jual PasarKita</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola keunggulan toko yang tampil di beranda untuk membangun kepercayaan calon pembeli.</p>
        </div>
        <button @click="addModal = true" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Tambah Keunggulan
        </button>
    </div>

    <!-- Grid Keunggulan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($features as $f)
            <div class="bg-white border border-slate-200 p-5 space-y-3 flex flex-col justify-between">
                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-400">#0{{ $f->sort_order }}</span>
                    <h3 class="font-bold text-slate-900 text-sm uppercase">{{ $f->title }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ $f->description }}</p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <form action="{{ route('admin.content.features.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Hapus keunggulan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-4 p-8 border border-dashed border-slate-200 bg-white text-center text-xs text-slate-400">
                Belum ada data pilar keunggulan toko.
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah Keunggulan -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Pilar Keunggulan</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.content.features.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Judul Keunggulan *</label>
                    <input type="text" name="title" required placeholder="Contoh: 100% Produk Original Butik" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Deskripsi Nilai Jual *</label>
                    <textarea name="description" rows="3" required placeholder="Jelaskan garansi, kualitas, atau keunggulan layanan..." class="w-full px-3 py-2 border border-slate-300"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nomor Urutan</label>
                    <input type="number" name="sort_order" value="1" min="1" max="10" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

