<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi Akun FOOTWORK</title>
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
                            <h2 style="margin: 0; font-size: 18px; font-weight: 700; color: #0a0a0a; text-align: center;">Atur Ulang Kata Sandi</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 24px; text-align: center;">
                            <p style="margin: 0; font-size: 13px; color: #525252; line-height: 1.6;">
                                Halo <strong>{{ $userName }}</strong>, kami menerima permintaan untuk mengatur ulang kata sandi akun FOOTWORK Anda. Silakan klik tombol di bawah ini untuk membuat kata sandi baru:
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background-color: #0a0a0a; color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; padding: 14px 32px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                Reset Kata Sandi
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 24px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #737373; line-height: 1.5;">
                                Tautan reset kata sandi ini akan kedaluwarsa dalam <strong>{{ $expireMinutes }} menit</strong>. Jika Anda tidak meminta reset kata sandi, abaikan email ini dan akun Anda tetap aman.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid #f0f0f0; padding-top: 20px; word-break: break-all;">
                            <p style="margin: 0 0 8px 0; font-size: 11px; color: #737373;">
                                Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #0a0a0a;">
                                <a href="{{ $resetUrl }}" style="color: #0a0a0a; text-decoration: underline;">{{ $resetUrl }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

