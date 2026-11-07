@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-20">
    <div class="border border-neutral-200/90 rounded-3xl p-8 sm:p-10 bg-white space-y-6 shadow-xs">
        <div class="text-center space-y-2">
            <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold">Portal Pelanggan</span>
            <h1 class="text-2xl font-extrabold text-neutral-950 tracking-tight">Masuk ke Akun Anda</h1>
            <p class="text-xs text-neutral-500 font-normal">Akses katalog Pre-Order eksklusif, buku alamat, dan riwayat pesanan.</p>
        </div>

        @if(session('success'))
            <div class="p-3.5 bg-neutral-900 text-white border border-neutral-800 rounded-xl text-xs font-medium flex items-start gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-green-400 text-sm mt-0.5 shrink-0"></i>
                <span class="leading-relaxed">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium flex items-start gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-sm mt-0.5 shrink-0"></i>
                <span class="leading-relaxed">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-xs font-bold text-neutral-900">Alamat Email *</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="nama@email.com"
                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
                @error('email')
                    <p class="text-[11px] text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1" x-data="{ showPassword: false }">
                <div class="flex justify-between items-center">
                    <label class="text-xs font-bold text-neutral-900">Kata Sandi *</label>
                    <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-neutral-500 hover:text-neutral-950 transition">
                        Lupa Password?
                    </a>
                </div>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           required 
                           placeholder="••••••••"
                           class="w-full text-xs px-3.5 py-2.5 pr-10 rounded-xl border border-neutral-200 focus:outline-none focus:border-neutral-950">
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-neutral-700 transition cursor-pointer p-1"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                        <i :class="showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'" class="text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-neutral-600">
                    <input type="checkbox" name="remember" class="text-neutral-950 focus:ring-0 rounded">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 bg-neutral-950 hover:bg-neutral-800 text-white text-xs uppercase tracking-widest font-bold rounded-xl active:scale-[0.98] transition shadow-xs hover:shadow-md cursor-pointer">
                Masuk ke Akun
            </button>
        </form>

        <div class="relative flex items-center justify-center pt-1">
            <div class="border-t border-neutral-200 w-full"></div>
            <span class="bg-white px-3 text-[11px] uppercase tracking-wider text-neutral-400 font-medium absolute">atau</span>
        </div>

        <a href="{{ route('auth.google') }}" 
           data-no-spa
           class="w-full py-3 px-4 border border-neutral-200 hover:border-neutral-950 bg-white hover:bg-neutral-50 text-neutral-800 text-xs font-bold rounded-xl transition flex items-center justify-center gap-3 shadow-2xs active:scale-[0.98] cursor-pointer">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.34 24 12 24z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
            </svg>
            <span>Masuk dengan Google</span>
        </a>

        <p class="text-xs text-center text-neutral-500 pt-2 border-t border-neutral-100">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-bold text-neutral-950 hover:underline">Daftar sekarang</a>
        </p>
    </div>
</div>
@endsection
