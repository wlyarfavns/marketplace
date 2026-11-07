<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthForgotPasswordTest extends TestCase
{
    public function test_forgot_password_page_is_accessible(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi?');
        $response->assertSee('Kirim Tautan Reset Sandi');
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

    public function test_send_reset_link_succeeds_and_dispatches_notification(): void
    {
        Notification::fake();

        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_send_reset_link_protects_against_account_enumeration(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'random_unregistered_person_9999@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_reset_password_page_is_accessible_with_token(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => 'sample-test-token',
            'email' => 'user@example.com',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Atur Ulang Kata Sandi');
        $response->assertSee('sample-test-token');
    }

    public function test_reset_password_validation_requires_confirmed_password(): void
    {
        $response = $this->post(route('password.update'), [
            'token' => 'test-token',
            'email' => 'user@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_reset_password_fails_with_invalid_token(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->post(route('password.update'), [
            'token' => 'completely-invalid-token',
            'email' => $user->email,
            'password' => 'newsecretpassword123',
            'password_confirmation' => 'newsecretpassword123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_successfully_reset_password_and_login(): void
    {
        // Create dedicated user for password reset testing
        $testUser = User::create([
            'name' => 'Tester Reset Password',
            'email' => 'test_reset_user_'.time().'@pasarkita.com',
            'password' => Hash::make('old_password_123'),
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $token = Password::broker()->createToken($testUser);
        $this->assertTrue(Password::broker()->tokenExists($testUser, $token));

        $newPassword = 'brand_new_secure_password_456';

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $testUser->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Verify password changed
        $testUser->refresh();
        $this->assertTrue(Hash::check($newPassword, $testUser->password));

        // Verify token consumed
        $this->assertFalse(Password::broker()->tokenExists($testUser, $token));

        // Verify login with new password
        $loginResponse = $this->post(route('login.post'), [
            'email' => $testUser->email,
            'password' => $newPassword,
        ]);

        $loginResponse->assertRedirect();
        $this->assertAuthenticatedAs($testUser);

        // Cleanup
        $testUser->delete();
    }
}
