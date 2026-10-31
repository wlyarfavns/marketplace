@extends('layouts.admin')

@section('page_title', '10. Banner Promosi Slider (Rasio 21:7)')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Manajemen Banner Slider Beranda</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola banner promo dengan rasio landscape 21:7 untuk tampilan beranda yang elegan.</p>
        </div>
        <button @click="addModal = true" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Tambah Banner Baru
        </button>
    </div>

    <!-- Grid Banner -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($banners as $b)
            <div class="bg-white border border-slate-200 p-5 space-y-3">
                <div class="aspect-[21/7] bg-slate-50 border border-slate-200 relative overflow-hidden flex items-center p-4">
                    @if($b->image_url)
                        <img src="{{ $b->image_url }}" alt="{{ $b->title }}" class="absolute right-0 top-0 bottom-0 w-1/2 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent"></div>
                    @endif
                    <div class="relative z-10 text-left">
                        <p class="font-bold text-sm text-slate-900">{{ $b->title }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $b->description }}</p>
                    </div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <div>
                        <span class="font-bold text-slate-800">Urutan: #{{ $b->sort_order }}</span>
                        @if($b->url)
                            <span class="text-slate-400 block text-[10px]">Tautan: {{ $b->url }}</span>
                        @endif
                    </div>
                    <form action="{{ route('admin.content.banners.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-red-500 hover:text-red-700">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-2 p-8 border border-dashed border-slate-200 bg-white text-center text-xs text-slate-400">
                Belum ada banner promosi yang diunggah.
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah Banner -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Banner Promo (21:7)</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.content.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Judul Banner *</label>
                    <input type="text" name="title" required placeholder="Official Drop: Branded Sneakers Collection" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Keterangan promo..." class="w-full px-3 py-2 border border-slate-300"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">URL / Tautan Tujuan</label>
                    <input type="text" name="url" placeholder="/katalog" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-700">Urutan Tampil</label>
                        <input type="number" name="sort_order" value="1" min="0" class="w-full px-3 py-2 border border-slate-300">
                    </div>

                    <div class="space-y-1">
                        <label class="font-semibold text-slate-700">Pilih Berkas Gambar *</label>
                        <input type="file" name="image" required accept="image/*" class="w-full px-2 py-1.5 border border-slate-300 bg-white">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="addModal = false" class="px-4 py-2 border border-slate-300 text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white font-bold uppercase tracking-wider">Simpan Banner</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

