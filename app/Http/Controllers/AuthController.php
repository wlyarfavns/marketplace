<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('account.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = strtolower(trim($credentials['email']));
        $password = $credentials['password'];

        $altEmail = str_ends_with($email, '@pasarkita.com')
            ? str_replace('@pasarkita.com', '@footwork.id', $email)
            : (str_ends_with($email, '@footwork.id') ? str_replace('@footwork.id', '@pasarkita.com', $email) : null);

        $attemptSuccess = Auth::attempt(['email' => $email, 'password' => $password], $request->boolean('remember')) ||
            ($altEmail && Auth::attempt(['email' => $altEmail, 'password' => $password], $request->boolean('remember')));

        if ($attemptSuccess) {
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            if ($user->role !== 'admin' && ! $user->email_verified_at) {
                Auth::logout();
                $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                $user->update([
                    'otp_code' => $otp,
                    'otp_expires_at' => now()->addMinutes(10),
                ]);

                try {
                    Mail::to($user->email)->send(new OtpMail($otp, $user->name));
                } catch (\Throwable $e) {
                    report($e);
                }

                session(['pending_verification_user_id' => $user->id]);

                return redirect()->route('otp.verify')
                    ->with('info', 'Email Anda belum diverifikasi. Kode OTP baru telah dikirimkan ke email Anda.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, '.$user->name);
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
        ]);

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'email_verified_at' => null,
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user->name));
        } catch (\Throwable $e) {
            report($e);
        }

        session(['pending_verification_user_id' => $user->id]);

        return redirect()->route('otp.verify')->with('success', 'Pendaftaran berhasil! Masukkan kode OTP yang telah dikirim ke email Anda.');
    }

    public function showVerifyOtp()
    {
        $userId = session('pending_verification_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('register')->with('error', 'Sesi verifikasi tidak ditemukan. Silakan daftar kembali.');
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('info', 'Akun Anda sudah terverifikasi. Silakan masuk.');
        }

        return view('auth.verify-otp', compact('user'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('pending_verification_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('register')->with('error', 'Sesi verifikasi tidak ditemukan. Silakan daftar kembali.');
        }

        if (! $user->otp_code || ! $user->otp_expires_at || now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP telah kadaluarsa. Silakan klik kirim ulang kode.']);
        }

        if ($user->otp_code !== trim($request->otp)) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        session()->forget('pending_verification_user_id');
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Verifikasi berhasil! Selamat datang di FOOTWORK.');
    }

    public function resendOtp()
    {
        $userId = session('pending_verification_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('register')->with('error', 'Sesi verifikasi tidak ditemukan.');
        }

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user->name));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Kode OTP baru telah berhasil dikirim ke '.$user->email);
    }

    public function showAdminLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $credentials['email'];
        $altEmail = str_ends_with($email, '@pasarkita.com')
            ? str_replace('@pasarkita.com', '@footwork.id', $email)
            : (str_ends_with($email, '@footwork.id') ? str_replace('@footwork.id', '@pasarkita.com', $email) : null);

        $attemptSuccess = Auth::attempt($credentials, $request->boolean('remember')) ||
            ($altEmail && Auth::attempt(['email' => $altEmail, 'password' => $credentials['password']], $request->boolean('remember')));

        if ($attemptSuccess) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'admin') {
                Auth::logout();

                return back()->withErrors(['email' => 'Akun Anda tidak memiliki hak akses administrator.']);
            }

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi admin salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }
}
