<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Kata Sandi</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f7f7f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #171717;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f7f7f7; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 480px; background-color: #ffffff; border-radius: 20px; border: 1px solid #e5e5e5; overflow: hidden; padding: 36px 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <span style="font-size: 22px; font-weight: 900; letter-spacing: 0.15em; color: #0a0a0a; text-transform: uppercase;">FOOTWORK.</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 12px;">
                            <h2 style="margin: 0; font-size: 18px; font-weight: 700; color: #0a0a0a; text-align: center;">Permintaan Reset Kata Sandi</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 24px; text-align: center;">
                            <p style="margin: 0; font-size: 13px; color: #525252; line-height: 1.6;">
                                Halo <strong>{{ $userName }}</strong>, kami menerima permintaan untuk mengatur ulang kata sandi akun FOOTWORK Anda. Gunakan 6 digit kode OTP di bawah ini untuk memverifikasi permintaan Anda:
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <div style="display: inline-block; background-color: #0a0a0a; color: #ffffff; font-size: 32px; font-weight: 800; letter-spacing: 12px; padding: 14px 28px 14px 40px; border-radius: 14px;">
                                {{ $otpCode }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 24px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #737373; line-height: 1.5;">
                                Kode OTP ini berlaku selama <strong>{{ $expireMinutes }} menit</strong>. Demi keamanan akun Anda, <strong>jangan bagikan kode ini kepada siapa pun</strong> termasuk pihak FOOTWORK.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid #f0f0f0; padding-top: 20px; text-align: center;">
                            <p style="margin: 0; font-size: 11px; color: #a3a3a3; line-height: 1.5;">
                                Jika Anda tidak merasa melakukan permintaan reset kata sandi, silakan abaikan email ini. Kata sandi akun Anda tidak akan berubah.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

