<?php

namespace Tests\Feature\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
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

    public function test_user_can_list_categories(): void
    {
        [$user, $token] = $this->authenticate();
        \App\Models\Category::factory()->count(2)->create();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/v1/categories');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'type', 'color']
                ]
            ]);
    }

    public function test_user_can_create_category(): void
    {
        [$user, $token] = $this->authenticate();
        $payload = [
            'name' => 'Custom Category',
            'type' => 'expense',
            'color' => '#123456',
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/v1/categories', $payload);
        $response->assertStatus(201)
            ->assertJson(['data' => ['name' => 'Custom Category', 'type' => 'expense', 'color' => '#123456']]);
    }

    public function test_user_can_view_single_category(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/v1/categories/' . $category->id);
        $response->assertStatus(200)
            ->assertJson(['data' => ['id' => $category->id]]);
    }

    public function test_user_can_update_category(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create(['user_id' => $user->id]);
        $payload = [
            'name' => 'Updated Category',
            'type' => 'income',
            'color' => '#654321',
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/v1/categories/' . $category->id, $payload);
        $response->assertStatus(200)
            ->assertJson(['data' => ['name' => 'Updated Category', 'type' => 'income', 'color' => '#654321']]);
    }

    public function test_user_can_delete_category(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create(['user_id' => $user->id]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/v1/categories/' . $category->id);
        $response->assertStatus(204);
    }
}
