<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_learner_and_signs_them_in(): void
    {
        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'register',
            ]),
        ]);

        $response = $this->post(route('register.store'), [
            'name' => 'Rani Putri',
            'email' => 'rani@example.com',
            'password' => 'Belajar123!',
            'password_confirmation' => 'Belajar123!',
            'cf-turnstile-response' => 'valid-turnstile-token',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'rani@example.com',
            'role' => 'user',
        ]);
        Http::assertSent(fn (ClientRequest $request): bool => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request['secret'] === 'test-secret-key'
            && $request['response'] === 'valid-turnstile-token');
    }

    public function test_user_can_sign_in_with_valid_credentials(): void
    {
        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'login',
            ]),
        ]);

        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'password' => 'Belajar123!',
            'role' => 'user',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Belajar123!',
            'cf-turnstile-response' => 'valid-turnstile-token',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        Http::assertSent(fn (ClientRequest $request): bool => $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $request['secret'] === 'test-secret-key'
            && $request['response'] === 'valid-turnstile-token');
    }

    public function test_registration_rejects_a_turnstile_token_for_another_action(): void
    {
        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'login',
            ]),
        ]);

        $this->post(route('register.store'), [
            'name' => 'Rani Putri',
            'email' => 'rani@example.com',
            'password' => 'Belajar123!',
            'password_confirmation' => 'Belajar123!',
            'cf-turnstile-response' => 'wrong-action-token',
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertDatabaseMissing('users', ['email' => 'rani@example.com']);
    }

    public function test_login_rejects_an_invalid_turnstile_token(): void
    {
        config([
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ]),
        ]);

        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'password' => 'Belajar123!',
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Belajar123!',
            'cf-turnstile-response' => 'invalid-turnstile-token',
        ])->assertSessionHasErrors('cf-turnstile-response');

        $this->assertGuest();
    }

    public function test_login_requires_a_turnstile_token_before_contacting_cloudflare(): void
    {
        Http::fake();

        $this->post(route('login.store'), [
            'email' => 'learner@example.com',
            'password' => 'Belajar123!',
        ])->assertSessionHasErrors('cf-turnstile-response');

        Http::assertNothingSent();
        $this->assertGuest();
    }

    public function test_dashboard_is_selected_from_the_signed_in_users_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertViewIs('dashboard.admin');

        $learner = User::factory()->create(['role' => 'user']);
        $this->actingAs($learner)->get(route('dashboard'))->assertOk()->assertViewIs('dashboard.user');
    }
}
