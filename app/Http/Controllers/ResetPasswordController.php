<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, ?string $token = null)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = $request->email ?: session('password_reset_authorized_email');

        if (! $token || ! $email) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi reset kata sandi tidak valid. Silakan ajukan permintaan Lupa Password.');
        }

        // Validate token exists in database (OTP verified token or broker token)
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();
        $user = User::where('email', $email)->first();

        $isValidOtpReset = $record && (
            (session('password_reset_authorized_token') === $token) ||
            Hash::check($token, $record->token)
        ) && Carbon::parse($record->created_at)->addMinutes(30)->isFuture();

        $isValidBrokerToken = $user && Password::broker()->tokenExists($user, $token);

        if (! $isValidOtpReset && ! $isValidBrokerToken) {
            return redirect()->route('password.request')
                ->with('error', 'Tautan atau sesi reset kata sandi tidak valid atau telah kedaluwarsa. Silakan verifikasi ulang OTP.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'token.required' => 'Token reset kata sandi tidak valid.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Pengguna dengan alamat email ini tidak ditemukan.']);
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        $isValidOtpReset = $record && (
            (session('password_reset_authorized_token') === $request->token) ||
            Hash::check($request->token, $record->token)
        ) && Carbon::parse($record->created_at)->addMinutes(30)->isFuture();

        $isValidBrokerToken = Password::broker()->tokenExists($user, $request->token);

        if (! $isValidOtpReset && ! $isValidBrokerToken) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Token reset kata sandi tidak valid atau telah kedaluwarsa. Silakan ajukan permintaan baru.']);
        }

        // Update user password securely
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        // Consume and delete the token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Clear all password reset sessions
        session()->forget([
            'password_reset_email',
            'password_reset_authorized_token',
            'password_reset_authorized_email',
            'password_reset_otp_sent_at',
        ]);

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil diatur ulang! Silakan masuk dengan kata sandi baru Anda.');
    }
}
