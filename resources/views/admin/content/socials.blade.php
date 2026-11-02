@extends('layouts.admin')

@section('page_title', '14. Media Sosial FOOTWORK')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Tautan Media Sosial Toko</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola akun resmi Instagram, TikTok, YouTube yang tampil di Top Bar & Footer beranda.</p>
        </div>
        <button @click="addModal = true" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Tambah Media Sosial
        </button>
    </div>

    <!-- Tabel Media Sosial -->
    <div class="bg-white border border-slate-200 overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="p-4">Platform</th>
                    <th class="p-4">URL Profil Akun</th>
                    <th class="p-4 text-center">Urutan</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($socials as $s)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4 font-bold text-slate-900 text-sm">
                            <i class="{{ $s->icon_class ?? 'fa-solid fa-link' }} mr-2"></i>
                            {{ $s->platform }}
                        </td>
                        <td class="p-4 text-blue-600">
                            <a href="{{ $s->url }}" target="_blank" class="hover:underline">{{ $s->url }}</a>
                        </td>
                        <td class="p-4 text-center font-bold text-slate-600">#{{ $s->sort_order }}</td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.content.socials.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus tautan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-slate-400">Belum ada akun media sosial yang didaftarkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Medsos -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Media Sosial</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.content.socials.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Platform *</label>
                    <select name="platform" required class="w-full px-3 py-2 border border-slate-300 bg-white">
                        <option value="Instagram">Instagram</option>
                        <option value="TikTok">TikTok</option>
                        <option value="YouTube">YouTube</option>
                        <option value="Facebook">Facebook</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">URL / Tautan Profil Lengkap *</label>
                    <input type="url" name="url" required placeholder="https://instagram.com/footwork.official" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Urutan</label>
                    <input type="number" name="sort_order" value="1" min="1" class="w-full px-3 py-2 border border-slate-300">
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

