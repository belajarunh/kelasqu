<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_learner_and_signs_them_in(): void
    {
        $this->withSession(['auth_captcha_answer' => 7]);

        $response = $this->post(route('register.store'), [
            'name' => 'Rani Putri',
            'email' => 'rani@example.com',
            'password' => 'Belajar123!',
            'password_confirmation' => 'Belajar123!',
            'captcha_answer' => '7',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'rani@example.com',
            'role' => 'user',
        ]);
    }

    public function test_user_can_sign_in_with_valid_credentials(): void
    {
        $this->withSession(['auth_captcha_answer' => 7]);

        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'password' => 'Belajar123!',
            'role' => 'user',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Belajar123!',
            'captcha_answer' => '7',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_an_incorrect_captcha_answer(): void
    {
        $this->withSession(['auth_captcha_answer' => 7])
            ->post(route('register.store'), [
                'name' => 'Rani Putri',
                'email' => 'rani@example.com',
                'password' => 'Belajar123!',
                'password_confirmation' => 'Belajar123!',
                'captcha_answer' => '8',
            ])
            ->assertSessionHasErrors('captcha_answer');

        $this->assertDatabaseMissing('users', ['email' => 'rani@example.com']);
    }

    public function test_login_rejects_an_incorrect_captcha_answer(): void
    {
        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'password' => 'Belajar123!',
        ]);

        $this->withSession(['auth_captcha_answer' => 7])
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'Belajar123!',
                'captcha_answer' => '8',
            ])
            ->assertSessionHasErrors('captcha_answer');

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