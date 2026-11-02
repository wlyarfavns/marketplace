<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Customer / Member Login
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

        $email = $credentials['email'];
        $altEmail = str_ends_with($email, '@footwork.id')
            ? str_replace('@footwork.id', '@pasarkita.com', $email)
            : (str_ends_with($email, '@pasarkita.com') ? str_replace('@pasarkita.com', '@footwork.id', $email) : null);

        $attemptSuccess = Auth::attempt($credentials, $request->boolean('remember')) ||
            ($altEmail && Auth::attempt(['email' => $altEmail, 'password' => $credentials['password']], $request->boolean('remember')));

        if ($attemptSuccess) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, '.Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'Kredensial yang dimasukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    // Customer Register
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'city' => $validated['city'] ?? null,
            'province' => $validated['province'] ?? null,
            'role' => 'customer',
            'customer_type' => 'member', // Pendaftar baru otomatis terdaftar sebagai member
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Pendaftaran berhasil! Akun Anda aktif dengan status Member.');
    }

    // Admin Login
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
        $altEmail = str_ends_with($email, '@footwork.id')
            ? str_replace('@footwork.id', '@pasarkita.com', $email)
            : (str_ends_with($email, '@pasarkita.com') ? str_replace('@pasarkita.com', '@footwork.id', $email) : null);

        $attemptSuccess = Auth::attempt($credentials, $request->boolean('remember')) ||
            ($altEmail && Auth::attempt(['email' => $altEmail, 'password' => $credentials['password']], $request->boolean('remember')));

        if ($attemptSuccess) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'admin') {
                Auth::logout();

                return back()->withErrors(['email' => 'Akun Anda tidak memiliki hak akses administrator.']);
            }

            return redirect()->intended(route('admin.dashboard'));
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
