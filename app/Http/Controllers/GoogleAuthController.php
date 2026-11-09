<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // Handle user cancellation or OAuth error
        if ($request->has('error') || ! $request->has('code')) {
            return redirect()->route('login')
                ->with('error', 'Login dengan Google dibatalkan atau terjadi kesalahan.');
        }

        try {
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
                // Fallback to stateless if browser session state was desynced/lost
                $googleUser = Socialite::driver('google')->stateless()->user();
            }
        } catch (\Throwable $e) {
            report($e);

            $errorMessage = 'Gagal menyinkronkan data dengan Google. Silakan coba kembali.';

            if (config('app.debug')) {
                $msg = $e->getMessage();
                if (str_contains($msg, 'invalid_client') || str_contains($msg, 'client secret')) {
                    $errorMessage = 'Google OAuth Error: GOOGLE_CLIENT_SECRET pada file .env belum diisi atau tidak valid (masih bernilai placeholder). Silakan periksa Client Secret di Google Cloud Console.';
                } elseif (str_contains($msg, 'redirect_uri_mismatch')) {
                    $errorMessage = 'Google OAuth Error: Redirect URI tidak cocok. Pastikan callback URL http://localhost:8000/auth/google/callback terdaftar di Authorized redirect URIs Google Cloud Console.';
                } else {
                    $errorMessage = 'Gagal menyinkronkan data dengan Google: '.$msg;
                }
            }

            return redirect()->route('login')
                ->with('error', $errorMessage);
        }

        $email = $googleUser->getEmail();
        if (! $email) {
            return redirect()->route('login')
                ->with('error', 'Akun Google Anda tidak menyediakan alamat email yang valid.');
        }

        // Validate verified email status from Google payload
        $rawPayload = $googleUser->getRaw();
        if (isset($rawPayload['email_verified']) && ! $rawPayload['email_verified']) {
            return redirect()->route('login')
                ->with('error', 'Alamat email akun Google Anda belum diverifikasi oleh Google.');
        }

        $googleId = (string) $googleUser->getId();

        // 1. Check if user already linked with this Google ID
        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            if ($user->status !== 'active') {
                return redirect()->route('login')
                    ->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.');
            }

            $request->session()->regenerate();
            Auth::login($user, true);

            return $user->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, '.$user->name);
        }

        // 2. Check if account already exists with the same email
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if ($existingUser->status !== 'active') {
                return redirect()->route('login')
                    ->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.');
            }

            // Link Google ID safely without altering user password
            $existingUser->update([
                'google_id' => $googleId,
                'email_verified_at' => $existingUser->email_verified_at ?? now(),
            ]);

            $request->session()->regenerate();
            Auth::login($existingUser, true);

            return $existingUser->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->intended(route('home'))->with('success', 'Akun Google berhasil ditautkan! Selamat datang kembali, '.$existingUser->name);
        }

        // 3. User does not exist, register new customer account
        $newUser = User::create([
            'name' => $googleUser->getName() ?: 'Pelanggan Google',
            'email' => $email,
            'google_id' => $googleId,
            'password' => Hash::make(Str::random(32)),
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $request->session()->regenerate();
        Auth::login($newUser, true);

        return redirect()->intended(route('home'))
            ->with('success', 'Pendaftaran berhasil menggunakan akun Google! Selamat datang di FOOTWORK.');
    }
}

