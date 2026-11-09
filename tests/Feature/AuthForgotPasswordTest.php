<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthForgotPasswordTest extends TestCase
{
    public function test_forgot_password_page_is_accessible(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi?');
        $response->assertSee('Kirim Kode OTP Reset Sandi');
    }

    public function test_authenticated_user_cannot_access_forgot_password_page(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get(route('password.request'));
        $response->assertRedirect(route('home'));
    }

    public function test_forgot_password_validation_fails_on_invalid_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_send_reset_otp_succeeds_and_sends_mail(): void
    {
        Mail::fake();

        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response->assertRedirect(route('password.otp.verify'));
        $response->assertSessionHas('status');

        Mail::assertSent(ResetPasswordOtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && strlen($mail->otpCode) === 6;
        });

        // Verify OTP is hashed in password_reset_tokens
        $record = DB::table('password_reset_tokens')->where('email', $user->email)->first();
        $this->assertNotNull($record);
        $this->assertNotEquals(6, strlen($record->token)); // Must be bcrypt hash, not plain 6 digit
        $this->assertTrue(str_starts_with($record->token, '$2y$'));
    }

    public function test_send_reset_otp_protects_against_account_enumeration(): void
    {
        Mail::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'unregistered_ghost_'.time().'@example.com',
        ]);

        $response->assertRedirect(route('password.otp.verify'));
        Mail::assertNothingSent();
    }

    public function test_verify_otp_page_is_accessible_with_session(): void
    {
        $response = $this->withSession([
            'password_reset_email' => 'customer@example.com',
        ])->get(route('password.otp.verify'));

        $response->assertStatus(200);
        $response->assertSee('Masukkan Kode OTP');
        $response->assertSee('customer@example.com');
    }

    public function test_verify_otp_page_redirects_if_session_email_missing(): void
    {
        $response = $this->get(route('password.otp.verify'));
        $response->assertRedirect(route('password.request'));
    }

    public function test_verify_otp_fails_with_wrong_code(): void
    {
        $email = 'tester_wrong_otp_'.time().'@pasarkita.com';
        $correctOtp = '123456';

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($correctOtp), 'created_at' => now()]
        );

        $response = $this->withSession(['password_reset_email' => $email])
            ->post(route('password.otp.verify.post'), [
                'otp' => '999999',
            ]);

        $response->assertSessionHasErrors('otp');
    }

    public function test_verify_otp_fails_when_expired(): void
    {
        $email = 'tester_expired_otp_'.time().'@pasarkita.com';
        $otp = '654321';

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($otp), 'created_at' => Carbon::now()->subMinutes(11)]
        );

        $response = $this->withSession(['password_reset_email' => $email])
            ->post(route('password.otp.verify.post'), [
                'otp' => $otp,
            ]);

        $response->assertSessionHasErrors('otp');
    }

    public function test_verify_otp_succeeds_and_redirects_to_reset_form(): void
    {
        $email = 'tester_valid_otp_'.time().'@pasarkita.com';
        $otp = '582194';

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($otp), 'created_at' => now()]
        );

        $response = $this->withSession(['password_reset_email' => $email])
            ->post(route('password.otp.verify.post'), [
                'otp' => $otp,
            ]);

        $response->assertRedirect();
        $this->assertStringContainsString('/reset-password/', $response->headers->get('Location'));
        $response->assertSessionHas('success');
    }

    public function test_direct_access_to_reset_password_page_without_valid_token_is_blocked(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => 'invalid-fake-token',
            'email' => 'victim@example.com',
        ]));

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('error');
    }

    public function test_user_can_successfully_reset_password_after_otp_and_login(): void
    {
        $testUser = User::create([
            'name' => 'OTP Reset Tester',
            'email' => 'otp_reset_user_'.time().'@pasarkita.com',
            'password' => Hash::make('old_secret_pass_123'),
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $otp = '849201';
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $testUser->email],
            ['token' => Hash::make($otp), 'created_at' => now()]
        );

        // 1. Verify OTP
        $verifyResponse = $this->withSession(['password_reset_email' => $testUser->email])
            ->post(route('password.otp.verify.post'), [
                'otp' => $otp,
            ]);

        $verifyResponse->assertRedirect();
        $resetUrl = $verifyResponse->headers->get('Location');

        // Extract token from reset URL
        preg_match('#/reset-password/([^?]+)#', $resetUrl, $matches);
        $resetToken = $matches[1] ?? null;
        $this->assertNotEmpty($resetToken);

        // 2. Open reset page with authorized token
        $formResponse = $this->get($resetUrl);
        $formResponse->assertStatus(200);
        $formResponse->assertSee('Atur Ulang Kata Sandi');

        // 3. Submit new password
        $newPassword = 'NewSecretPassword2026!';
        $resetResponse = $this->post(route('password.update'), [
            'token' => $resetToken,
            'email' => $testUser->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $resetResponse->assertRedirect(route('login'));
        $resetResponse->assertSessionHas('success');

        // 4. Verify password changed in DB
        $testUser->refresh();
        $this->assertTrue(Hash::check($newPassword, $testUser->password));
        $this->assertFalse(Hash::check('old_secret_pass_123', $testUser->password));

        // 5. Verify token was deleted (single-use)
        $this->assertNull(DB::table('password_reset_tokens')->where('email', $testUser->email)->first());

        // 6. Login with new password
        $loginResponse = $this->post(route('login.post'), [
            'email' => $testUser->email,
            'password' => $newPassword,
        ]);

        $loginResponse->assertRedirect();
        $this->assertAuthenticatedAs($testUser);

        // 7. Old password cannot login
        $this->post(route('logout'));
        $failedLogin = $this->post(route('login.post'), [
            'email' => $testUser->email,
            'password' => 'old_secret_pass_123',
        ]);
        $this->assertGuest();

        // Cleanup
        $testUser->delete();
    }
}
