<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_the_forgot_password_form(): void
    {
        $this->get('/forgot-password')
            ->assertInertia(fn(Assert $page) => $page->component('auth/ForgotPassword'));
    }

    public function test_forgot_password_sends_a_reset_link_to_the_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->from('/forgot-password')->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect('/forgot-password')
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_form_receives_the_email_and_token(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]))->assertInertia(fn(Assert $page) => $page
            ->component('auth/ResetPassword')
            ->where('email', $user->email)
            ->where('token', $token));
    }

    public function test_valid_reset_token_updates_the_password(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post('/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'New-password-123',
            'password_confirmation' => 'New-password-123',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('New-password-123', $user->fresh()->password));
    }

    public function test_password_reset_rejects_a_mismatched_confirmation(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->from(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]))->post('/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'New-password-123',
            'password_confirmation' => 'Different-password-123',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
