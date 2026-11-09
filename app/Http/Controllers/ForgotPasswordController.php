<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
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

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Tautan untuk mengatur ulang kata sandi telah dikirimkan ke alamat email Anda.');
        }

        // Return a consistent message to prevent account enumeration attacks
        return back()->with('status', 'Jika alamat email terdaftar di sistem kami, tautan reset kata sandi telah dikirimkan.');
    }
}

