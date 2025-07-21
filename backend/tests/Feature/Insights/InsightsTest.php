<?php

namespace Tests\Feature\Insights;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InsightsTest extends TestCase
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

    protected function authenticate()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        return [$user, $token];
    }

    public function test_user_can_view_insights(): void
    {
        [$user, $token] = $this->authenticate();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/v1/insights');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'summary',
                    'anomalies',
                    'suggestions',
                ]
            ]);
    }

    public function test_guest_cannot_view_insights(): void
    {
        $response = $this->getJson('/v1/insights');
        $response->assertStatus(401);
    }
}
