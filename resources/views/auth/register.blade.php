@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto px-4 py-20">
    <div class="border border-gray-200 p-8 sm:p-10 bg-white space-y-6">
        <div class="text-center space-y-2">
            <span class="text-xs uppercase tracking-widest text-black font-bold">Registrasi Pelanggan</span>
            <h1 class="text-2xl font-serif font-bold text-gray-950">Daftar Akun Member Baru</h1>
            <p class="text-xs text-gray-500 font-light">Buka akses katalog Pre-Order sepatu branded, kupon khusus member, dan fitur pelacakan pesanan.</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-700">Nama Lengkap *</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="Nama Anda"
                       class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                @error('name')
                    <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Alamat Email *</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="nama@email.com"
                           class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                    @error('email')
                        <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Nomor WhatsApp *</label>
                    <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                           class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                    @error('phone')
                        <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Kota / Kabupaten</label>
                    <input type="text" name="city" value="{{ old('city') }}" placeholder="Kota Domisili"
                           class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Provinsi</label>
                    <input type="text" name="province" value="{{ old('province') }}" placeholder="Provinsi"
                           class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Kata Sandi (Min 6 Karakter) *</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                    @error('password')
                        <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-gray-700">Konfirmasi Kata Sandi *</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••"
                           class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3.5 bg-black text-white text-xs uppercase tracking-widest font-semibold hover:bg-black transition">
                    Daftar Sebagai Member
                </button>
            </div>
        </form>

        <div class="text-center pt-2 border-t border-gray-100 text-xs text-gray-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-semibold text-black underline underline-offset-2">Masuk ke Akun</a>
        </div>
    </div>
</div>
@endsection

