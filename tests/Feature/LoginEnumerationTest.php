<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginEnumerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_email_and_wrong_password_are_indistinguishable(): void
    {
        User::factory()->create([
            'email' => 'known@example.com',
            'password' => 'correct-password',
        ]);

        $known = $this->postJson('/api/auth/login', [
            'email' => 'known@example.com',
            'password' => 'wrong-password',
        ]);

        $unknown = $this->postJson('/api/auth/login', [
            'email' => 'nobody@example.com',
            'password' => 'wrong-password',
        ]);

        $known->assertUnauthorized();
        $unknown->assertUnauthorized();

        $this->assertSame(
            $known->json('message'),
            $unknown->json('message'),
            'Login response must not reveal whether an email is registered.'
        );

        $this->assertStringNotContainsStringIgnoringCase(
            'no account',
            (string) $unknown->json('message')
        );
    }

    public function test_valid_credentials_still_return_a_token(): void
    {
        User::factory()->create([
            'email' => 'known@example.com',
            'password' => 'correct-password',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'known@example.com',
            'password' => 'correct-password',
        ])
            ->assertOk()
            ->assertJsonStructure(['status', 'message', 'data' => ['access_token', 'token_type', 'expires_in']]);
    }
}