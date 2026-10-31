@extends('layouts.app')

@section('content')
<div class="border-b border-gray-200 bg-gray-50 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-8 flex justify-between items-center">
        <div>
            <span class="text-xs uppercase tracking-widest text-black font-bold">Pengaturan Akun</span>
            <h1 class="text-3xl font-serif font-black text-gray-950 mt-1">Profil & Preferensi Belanja</h1>
        </div>
        <a href="{{ route('account.dashboard') }}" class="inline-flex items-center gap-2 border-2 border-gray-300 bg-white px-4 py-2 rounded text-xs uppercase font-bold text-gray-700 hover:text-black hover:border-black transition shadow-2xs active:translate-y-0.5 cursor-pointer">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Kembali ke Dasbor</span>
        </a>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-8 py-12 space-y-12">
    
    <!-- Form Profil & Buku Alamat Default -->
    <div class="border-2 border-gray-200 rounded-lg bg-white p-8 space-y-6 shadow-2xs">
        <div>
            <h3 class="text-base font-serif font-bold text-gray-950 uppercase tracking-wide">Data Diri & Alamat Pengiriman Default</h3>
            <p class="text-xs text-gray-500 mt-0.5">Informasi ini akan terisi secara otomatis setiap kali Anda melakukan checkout.</p>
        </div>

        <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700">Nama Lengkap *</label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                           class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black font-medium">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700">Nomor WhatsApp *</label>
                    <input type="text" name="phone" required value="{{ old('phone', $user->phone) }}"
                           class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black font-medium">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-700">Alamat Lengkap Default</label>
                <textarea name="address" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan..."
                          class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black">{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700">Kota / Kabupaten</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}"
                           class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700">Provinsi</label>
                    <input type="text" name="province" value="{{ old('province', $user->province) }}"
                           class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                           class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black">
                </div>
            </div>

            <!-- Preferensi Kemitraan: ID Seller & Ekspedisi Favorit -->
            <div class="pt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700">ID Seller / Mitra Langganan</label>
                    <input type="text" name="default_seller_code" value="{{ old('default_seller_code', $user->default_seller_code) }}" placeholder="Contoh: MITRA-001"
                           class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded uppercase focus:outline-hidden focus:border-black font-medium">
                    <p class="text-[10px] text-gray-400">Kode ini otomatis diaplikasikan saat Anda berbelanja.</p>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-700">Ekspedisi Favorit</label>
                    <select name="favorite_courier" class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded bg-white focus:outline-hidden focus:border-black font-medium cursor-pointer">
                        <option value="JNE" {{ $user->favorite_courier === 'JNE' ? 'selected' : '' }}>JNE Express</option>
                        <option value="SiCepat" {{ $user->favorite_courier === 'SiCepat' ? 'selected' : '' }}>SiCepat Express</option>
                        <option value="J&T" {{ $user->favorite_courier === 'J&T' ? 'selected' : '' }}>J&T Express</option>
                        <option value="POS" {{ $user->favorite_courier === 'POS' ? 'selected' : '' }}>POS Indonesia</option>
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="border-2 border-black bg-black text-white text-xs uppercase tracking-widest font-black px-8 py-3.5 hover:bg-neutral-800 transition shadow-md rounded active:translate-y-0.5 cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Form Ubah Kata Sandi Mandiri -->
    <div class="border-2 border-gray-200 rounded-lg bg-white p-8 space-y-6 shadow-2xs">
        <div>
            <h3 class="text-base font-serif font-bold text-gray-950 uppercase tracking-wide">Ubah Kata Sandi Mandiri</h3>
            <p class="text-xs text-gray-500 mt-0.5">Pastikan kata sandi baru Anda minimal 6 karakter kombinasi yang kuat.</p>
        </div>

        <form action="{{ route('account.password.update') }}" method="POST" class="space-y-4 max-w-lg">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-700">Kata Sandi Saat Ini *</label>
                <input type="password" name="current_password" required placeholder="••••••••"
                       class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black font-medium">
                @error('current_password')
                    <p class="text-[11px] text-red-600 font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-700">Kata Sandi Baru *</label>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black font-medium">
                @error('password')
                    <p class="text-[11px] text-red-600 font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-700">Ulangi Kata Sandi Baru *</label>
                <input type="password" name="password_confirmation" required placeholder="••••••••"
                       class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black font-medium">
            </div>

            <div class="pt-2">
                <button type="submit" class="border-2 border-black bg-white text-black text-xs uppercase tracking-widest font-bold px-8 py-3.5 hover:bg-black hover:text-white transition shadow-xs rounded active:translate-y-0.5 cursor-pointer">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
