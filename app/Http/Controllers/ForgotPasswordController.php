<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset OTP.
     */
    public function showLinkRequestForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.forgot-password');
    }

    /**
     * Generate and send a 6-digit OTP code to the user's email.
     */
    public function sendResetOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        // Anti-enumeration: save session email even if user does not exist
        session([
            'password_reset_email' => $email,
            'password_reset_otp_sent_at' => now()->timestamp,
        ]);

        if ($user && $user->status === 'active') {
            // Cryptographically secure 6-digit numeric OTP
            $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Store hashed OTP in password_reset_tokens table (never plaintext)
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($otp),
                    'created_at' => now(),
                ]
            );

            // Send OTP email synchronously using FOOTWORK branded template
            try {
                Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name, 10));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('password.otp.verify')
            ->with('status', 'Kode OTP telah dikirimkan ke alamat email Anda. Silakan periksa kotak masuk atau spam.');
    }

    /**
     * Display the OTP verification form for password reset.
     */
    public function showVerifyOtpForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = session('password_reset_email');
        if (! $email) {
            return redirect()->route('password.request')
                ->with('error', 'Silakan masukkan alamat email Anda terlebih dahulu.');
        }

        return view('auth.verify-reset-otp', compact('email'));
    }

    /**
     * Validate the submitted OTP code.
     */
    public function verifyOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 digit angka.',
        ]);

        $email = session('password_reset_email');
        if (! $email) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi verifikasi telah kedaluwarsa. Silakan ajukan permintaan ulang.');
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (! $record || ! $record->created_at) {
            return back()->withErrors(['otp' => 'Kode OTP tidak ditemukan atau telah kedaluwarsa. Silakan ajukan permintaan baru.']);
        }

        // OTP expiration check (10 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa (lebih dari 10 menit). Silakan klik kirim ulang kode.']);
        }

        // Verify OTP against hashed token
        if (! Hash::check(trim($request->otp), $record->token)) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        // OTP verified successfully! Generate a one-time authorized reset token
        $resetToken = Str::random(64);

        // Replace OTP with the authorized reset token
        DB::table('password_reset_tokens')->where('email', $email)->update([
            'token' => Hash::make($resetToken),
            'created_at' => now(),
        ]);

        // Mark session as authorized for reset password view
        session([
            'password_reset_authorized_email' => $email,
            'password_reset_authorized_token' => $resetToken,
        ]);

        return redirect()->route('password.reset', [
            'token' => $resetToken,
            'email' => $email,
        ])->with('success', 'Kode OTP berhasil diverifikasi! Silakan tentukan kata sandi baru Anda.');
    }

    /**
     * Resend a new OTP code to the user's email.
     */
    public function resendOtp(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = session('password_reset_email');
        if (! $email) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi verifikasi tidak ditemukan.');
        }

        $lastSent = session('password_reset_otp_sent_at', 0);
        if (now()->timestamp - $lastSent < 60) {
            $wait = 60 - (now()->timestamp - $lastSent);
            return back()->withErrors(['otp' => "Harap tunggu {$wait} detik sebelum meminta kode OTP baru."]);
        }

        $user = User::where('email', $email)->first();

        if ($user && $user->status === 'active') {
            $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($otp),
                    'created_at' => now(),
                ]
            );

            try {
                Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name, 10));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        session(['password_reset_otp_sent_at' => now()->timestamp]);

        return back()->with('status', 'Kode OTP baru telah berhasil dikirimkan ke email Anda.');
    }
}
