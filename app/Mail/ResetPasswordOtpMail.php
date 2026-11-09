<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otpCode,
        public string $userName = 'Pelanggan',
        public int $expireMinutes = 10
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Reset Kata Sandi FOOTWORK: '.$this->otpCode,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password-otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

