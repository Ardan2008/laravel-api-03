<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_without_credentials_returns_validation_error(): void
    {
        $this->postJson('/api/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_with_malformed_email_returns_validation_error(): void
    {
        $this->postJson('/api/auth/login', [
            'email' => 'not-an-email',
            'password' => 'secret123',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_login_with_missing_password_returns_validation_error(): void
    {
        User::factory()->create(['email' => 'real@example.com']);

        $this->postJson('/api/auth/login', ['email' => 'real@example.com'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_wrong_password_still_reports_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'real@example.com',
            'password' => 'secret123',
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'real@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(401)->assertJsonPath('message', 'The email or password is incorrect');
    }

    public function test_valid_login_returns_a_token(): void
    {
        User::factory()->create([
            'email' => 'real@example.com',
            'password' => 'secret123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'real@example.com',
            'password' => 'secret123',
        ])->assertOk();

        $payload = $response->json();
        $this->assertNotEmpty($payload, 'login returned an empty body');

        $token = data_get($payload, 'data.access_token')
            ?? data_get($payload, 'access_token');

        $this->assertIsString($token, 'token missing from: '.json_encode($payload));
        $this->assertNotSame('', $token);
    }
}