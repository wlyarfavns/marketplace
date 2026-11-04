@extends('layouts.admin')

@section('page_title', '13. Rekening Bank Transfer')

@section('content')
<div class="space-y-6" x-data="{ addModal: false }">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Rekening Tujuan Pembayaran Resmi</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar nomor rekening bank resmi yang tampil di halaman checkout dan konfirmasi pesanan pembeli.</p>
        </div>
        <button @click="addModal = true" class="bg-slate-900 text-white text-xs uppercase tracking-wider font-semibold px-4 py-2.5 hover:bg-slate-800 transition">
            + Tambah Rekening Bank
        </button>
    </div>

    <!-- Grid Bank -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @forelse($banks as $b)
            <div class="bg-white border border-slate-200 p-5 space-y-3 flex flex-col justify-between">
                <div class="space-y-1">
                    <span class="text-xs uppercase font-bold text-slate-400">Akun Transfer Resmi</span>
                    <h3 class="font-bold text-slate-900 text-base">{{ $b->bank_name }}</h3>
                    <p class="font-mono text-lg font-bold text-slate-900 select-all">{{ $b->account_number }}</p>
                    <p class="text-xs text-slate-600">a/n <strong class="text-slate-900">{{ $b->account_holder_name }}</strong></p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <form action="{{ route('admin.content.banks.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                            <i class="fa-solid fa-trash-can"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-8 border border-dashed border-slate-200 bg-white text-center text-xs text-slate-400">
                Belum ada rekening bank yang didaftarkan.
            </div>
        @endforelse
    </div>

    <div>
        {{ $banks->links() }}
    </div>

    <!-- Modal Tambah Bank -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div class="bg-white max-w-md w-full p-6 border border-slate-200 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Rekening Bank</h3>
                <button @click="addModal = false" class="text-slate-400 hover:text-black">&times;</button>
            </div>

            <form action="{{ route('admin.content.banks.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nama Bank *</label>
                    <input type="text" name="bank_name" required placeholder="Contoh: Bank Syariah Indonesia (BSI)" class="w-full px-3 py-2 border border-slate-300">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Nomor Rekening *</label>
                    <input type="text" name="account_number" required placeholder="7112233445" class="w-full px-3 py-2 border border-slate-300 font-mono">
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-700">Atas Nama Pemilik (A/N) *</label>
                    <input type="text" name="account_holder_name" required placeholder="PT FOOTWORK DIGITAL INDONESIA" class="w-full px-3 py-2 border border-slate-300 uppercase">
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

