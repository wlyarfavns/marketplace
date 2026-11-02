<?php

namespace Tests\Feature;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpRegistrationTest extends TestCase
{
    use DatabaseTransactions;
    public function test_registration_creates_pending_user_and_sends_otp(): void
    {
        Mail::fake();

        $email = 'test_runner_'.time().'@example.com';

        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
        ]);

        $response->assertRedirect('/verify-otp');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        $this->assertNotNull($user->otp_code);
        $this->assertEquals(6, strlen($user->otp_code));

        Mail::assertSent(OtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function test_user_can_verify_otp_and_login(): void
    {
        $user = User::create([
            'name' => 'Verif User',
            'email' => 'verif_'.time().'@example.com',
            'password' => 'password123',
            'phone' => '08987654321',
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
            'email_verified_at' => null,
        ]);

        $response = $this->withSession(['pending_verification_user_id' => $user->id])
            ->post('/verify-otp', [
                'otp' => '123456',
            ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->otp_code);
    }

    public function test_user_cannot_verify_with_wrong_otp(): void
    {
        $user = User::create([
            'name' => 'Wrong Otp User',
            'email' => 'wrong_'.time().'@example.com',
            'password' => 'password123',
            'phone' => '08987654321',
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
            'email_verified_at' => null,
        ]);

        $response = $this->withSession(['pending_verification_user_id' => $user->id])
            ->post('/verify-otp', [
                'otp' => '654321',
            ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();
    }
}

