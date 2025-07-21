<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'loginuser@example.com',
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'email' => 'loginuser@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/v1/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ]);
    }

    public function test_login_requires_valid_data(): void
    {
        $payload = [
            'email' => 'not-an-email',
            'password' => '',
        ];

        $response = $this->postJson('/v1/login', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'failuser@example.com',
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'email' => 'failuser@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/v1/login', $payload);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials.']);
    }
}
