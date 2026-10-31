@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-20">
    <div class="border-2 border-gray-200 rounded-lg p-8 sm:p-10 bg-white space-y-6 shadow-2xs">
        <div class="text-center space-y-2">
            <span class="text-xs uppercase tracking-widest text-black font-bold">Portal Pelanggan</span>
            <h1 class="text-2xl font-serif font-black text-gray-950">Masuk ke Akun Anda</h1>
            <p class="text-xs text-gray-500 font-normal">Akses katalog Prapesan eksklusif, buku alamat, dan riwayat pesanan sepatu.</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-xs font-bold text-gray-700">Alamat Email *</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="nama@email.com"
                       class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black font-medium">
                @error('email')
                    <p class="text-[11px] text-red-600 font-semibold mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-gray-700">Kata Sandi *</label>
                </div>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full text-xs px-3.5 py-2.5 border-2 border-gray-300 rounded focus:outline-hidden focus:border-black font-medium">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-gray-600 select-none">
                    <input type="checkbox" name="remember" class="text-black focus:ring-0">
                    <span class="font-medium">Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-4 border-2 border-black bg-black text-white text-xs uppercase tracking-widest font-black hover:bg-neutral-800 transition shadow-md rounded active:translate-y-0.5 cursor-pointer flex items-center justify-center gap-2">
                <span>Masuk ke Akun</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <div class="p-4 bg-gray-50 border-2 border-gray-200 rounded-lg text-xs space-y-2">
            <p class="font-bold text-gray-900 uppercase tracking-wide text-[10px]">Akun Demo Tersedia:</p>
            <div class="space-y-1 text-[11px] text-gray-600">
                <p><span class="font-bold text-gray-800">Member:</span> member@pasarkita.com (pass: password)</p>
                <p><span class="font-bold text-gray-800">Distributor:</span> distributor@pasarkita.com (pass: password)</p>
                <p><span class="font-bold text-gray-800">Customer Umum:</span> umum@pasarkita.com (pass: password)</p>
            </div>
        </div>

        <div class="text-center pt-2 border-t border-gray-100 text-xs text-gray-500">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-bold text-black underline underline-offset-2 hover:text-gray-700">Daftar Member Baru</a>
        </div>
    </div>
</div>
@endsection
