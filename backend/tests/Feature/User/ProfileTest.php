<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    use RefreshDatabase;

    public function test_user_can_view_profile(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'name' => 'Jane Doe',
                    'email' => 'jane@example.com',
                ],
            ]);
    }

    public function test_user_can_update_profile(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $payload = [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Jane Smith',
                    'email' => 'jane.smith@example.com',
                ],
            ]);
    }

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->getJson('/api/profile');
        $response->assertStatus(401);

        $response = $this->putJson('/api/profile', []);
        $response->assertStatus(401);
    }
}
