@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto px-4 py-20">
    <div class="border border-neutral-200/90 rounded-3xl p-8 sm:p-10 bg-white space-y-6 shadow-xs text-center">
        <div class="space-y-2">
            <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-neutral-100 flex items-center justify-center text-neutral-900">
                <i class="fa-solid fa-shield-halved text-xl"></i>
            </div>
            <span class="text-xs uppercase tracking-widest text-neutral-400 font-bold">Verifikasi Keamanan</span>
            <h1 class="text-2xl font-extrabold text-neutral-950 tracking-tight">Masukkan Kode OTP</h1>
            <p class="text-xs text-neutral-500 font-normal leading-relaxed">
                Kami telah mengirimkan 6 digit kode OTP untuk reset kata sandi ke:
                <br>
                <span class="font-bold text-neutral-900">{{ $email }}</span>
            </p>
        </div>

        @if(session('status'))
            <div class="p-3 bg-neutral-50 border border-neutral-200 rounded-xl text-xs text-neutral-800 font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if(session('success'))
            <div class="p-3 bg-neutral-50 border border-neutral-200 rounded-xl text-xs text-neutral-800 font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('otp'))
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-medium">
                {{ $errors->first('otp') }}
            </div>
        @endif

        <form action="{{ route('password.otp.verify.post') }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <input type="text" 
                       name="otp" 
                       maxlength="6" 
                       required 
                       autofocus 
                       placeholder="000000"
                       autocomplete="one-time-code"
                       pattern="\d{6}"
                       inputmode="numeric"
                       class="w-full text-center text-2xl font-extrabold tracking-[0.5em] px-4 py-3.5 rounded-xl border border-neutral-300 focus:outline-none focus:border-neutral-950 focus:ring-1 focus:ring-neutral-950 transition">
                <p class="text-[11px] text-neutral-400">Masukkan 6 digit angka tanpa spasi</p>
            </div>

            <button type="submit" class="w-full py-3.5 bg-neutral-950 hover:bg-neutral-800 text-white text-xs uppercase tracking-widest font-bold rounded-xl active:scale-[0.98] transition shadow-xs hover:shadow-md cursor-pointer">
                Verifikasi & Lanjutkan
            </button>
        </form>

        <div class="pt-4 border-t border-neutral-100 space-y-3" x-data="{ countdown: 60, timer: null }" x-init="timer = setInterval(() => { if (countdown > 0) countdown--; else clearInterval(timer); }, 1000)">
            <p class="text-xs text-neutral-500">
                Tidak menerima kode OTP di email?
            </p>
            
            <form action="{{ route('password.otp.resend') }}" method="POST">
                @csrf
                <button type="submit" 
                        :disabled="countdown > 0"
                        :class="countdown > 0 ? 'text-neutral-400 cursor-not-allowed' : 'text-neutral-950 hover:underline cursor-pointer font-bold'"
                        class="text-xs transition">
                    <span x-show="countdown > 0">Kirim ulang dalam (<span x-text="countdown"></span>s)</span>
                    <span x-show="countdown === 0">Kirim Ulang Kode OTP</span>
                </button>
            </form>

            <p class="text-[11px] text-neutral-400 pt-1">
                Salah memasukkan alamat email?
                <a href="{{ route('password.request') }}" class="text-neutral-900 underline hover:text-neutral-700">Ganti Email</a>
            </p>
        </div>
    </div>
</div>
@endsection

