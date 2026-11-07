<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthGoogleTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_page_renders_google_login_button(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee(route('auth.google'));
        $response->assertSee('Masuk dengan Google');
        $response->assertSee('data-no-spa', false);
    }

    public function test_redirect_to_google_initiates_oauth_redirect(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/v2/auth'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('auth.google'));
        $response->assertRedirect('https://accounts.google.com/o/oauth2/v2/auth');
    }

    public function test_authenticated_user_cannot_initiate_google_login(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get(route('auth.google'));
        $response->assertRedirect(route('home'));
    }

    public function test_google_callback_handles_user_cancellation(): void
    {
        $response = $this->get(route('auth.google.callback', [
            'error' => 'access_denied',
        ]));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_google_callback_rejects_unverified_email(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-id-unverified');
        $socialiteUser->shouldReceive('getEmail')->andReturn('unverified@example.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Unverified User');
        $socialiteUser->shouldReceive('getRaw')->andReturn(['email_verified' => false]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback', ['code' => 'dummy_code']));
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_google_callback_logs_in_existing_user_linked_by_google_id(): void
    {
        $user = User::create([
            'name' => 'Existing Google Member',
            'email' => 'google_existing_'.time().'@example.com',
            'google_id' => 'google-uid-777',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-uid-777');
        $socialiteUser->shouldReceive('getEmail')->andReturn($user->email);
        $socialiteUser->shouldReceive('getName')->andReturn($user->name);
        $socialiteUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback', ['code' => 'dummy_code']));
        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $user->delete();
    }

    public function test_google_callback_links_existing_user_by_email_without_overwriting_password(): void
    {
        $originalPasswordHash = Hash::make('super_secret_old_password');

        $user = User::create([
            'name' => 'Legacy Email User',
            'email' => 'legacy_email_'.time().'@example.com',
            'google_id' => null,
            'password' => $originalPasswordHash,
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'active',
            'email_verified_at' => null,
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('new-linked-google-id-999');
        $socialiteUser->shouldReceive('getEmail')->andReturn($user->email);
        $socialiteUser->shouldReceive('getName')->andReturn('Legacy Email User');
        $socialiteUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback', ['code' => 'dummy_code']));
        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertEquals('new-linked-google-id-999', $user->google_id);
        $this->assertEquals($originalPasswordHash, $user->password, 'Password must not be overwritten when linking Google account');
        $this->assertNotNull($user->email_verified_at);

        $user->delete();
    }

    public function test_google_callback_registers_new_customer(): void
    {
        $newEmail = 'brand_new_google_customer_'.time().'@example.com';
        $googleId = 'google-new-id-'.time();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($googleId);
        $socialiteUser->shouldReceive('getEmail')->andReturn($newEmail);
        $socialiteUser->shouldReceive('getName')->andReturn('Newbie Google');
        $socialiteUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback', ['code' => 'dummy_code']));
        $response->assertRedirect(route('home'));

        $createdUser = User::where('email', $newEmail)->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals('Newbie Google', $createdUser->name);
        $this->assertEquals($googleId, $createdUser->google_id);
        $this->assertEquals('customer', $createdUser->role);
        $this->assertEquals('member', $createdUser->customer_type);
        $this->assertEquals('active', $createdUser->status);
        $this->assertNotNull($createdUser->email_verified_at);
        $this->assertAuthenticatedAs($createdUser);

        $createdUser->delete();
    }

    public function test_google_callback_rejects_inactive_user(): void
    {
        $inactiveUser = User::create([
            'name' => 'Suspended Customer',
            'email' => 'suspended_'.time().'@example.com',
            'google_id' => 'suspended-google-id',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'customer_type' => 'member',
            'status' => 'inactive',
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('suspended-google-id');
        $socialiteUser->shouldReceive('getEmail')->andReturn($inactiveUser->email);
        $socialiteUser->shouldReceive('getName')->andReturn($inactiveUser->name);
        $socialiteUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback', ['code' => 'dummy_code']));
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();

        $inactiveUser->delete();
    }
}
