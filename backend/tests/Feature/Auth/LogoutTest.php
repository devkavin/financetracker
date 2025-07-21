<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
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

    public function test_user_can_logout(): void
    {
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/v1/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Logged out successfully.',
            ]);
    }

    public function test_guest_cannot_logout(): void
    {
        $response = $this->postJson('/v1/logout');
        $response->assertStatus(401);
    }
}
