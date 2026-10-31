@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-20">
    <div class="border border-gray-200 p-8 sm:p-10 bg-white space-y-6">
        <div class="text-center space-y-2">
            <span class="text-xs uppercase tracking-widest text-gray-400 font-semibold">Portal Pelanggan</span>
            <h1 class="text-2xl font-serif font-bold text-gray-950">Masuk ke Akun Anda</h1>
            <p class="text-xs text-gray-500 font-light">Akses katalog Pre-Order eksklusif, buku alamat, dan riwayat pesanan.</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-xs font-semibold text-gray-700">Alamat Email *</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="nama@email.com"
                       class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
                @error('email')
                    <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-semibold text-gray-700">Kata Sandi *</label>
                </div>
                <input type="password" name="password" required placeholder="••••••••"
                       class="w-full text-xs px-3.5 py-2.5 border border-gray-300 focus:outline-none focus:border-black">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-gray-600">
                    <input type="checkbox" name="remember" class="text-black focus:ring-0">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-black text-white text-xs uppercase tracking-widest font-semibold hover:bg-black transition">
                Masuk ke Akun
            </button>
        </form>

        <div class="p-4 bg-gray-50 border border-gray-200 text-xs space-y-2">
            <p class="font-bold text-gray-900 uppercase tracking-wide text-[10px]">Akun Demo Tersedia:</p>
            <div class="space-y-1 text-[11px] text-gray-600">
                <p><span class="font-semibold text-gray-800">Member:</span> member@pasarkita.com (pass: password)</p>
                <p><span class="font-semibold text-gray-800">Distributor:</span> distributor@pasarkita.com (pass: password)</p>
                <p><span class="font-semibold text-gray-800">Customer Umum:</span> umum@pasarkita.com (pass: password)</p>
            </div>
        </div>

        <div class="text-center pt-2 border-t border-gray-100 text-xs text-gray-500">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-semibold text-black underline underline-offset-2">Daftar Member Baru</a>
        </div>
    </div>
</div>
@endsection

