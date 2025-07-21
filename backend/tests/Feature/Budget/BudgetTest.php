<?php

namespace Tests\Feature\Budget;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BudgetTest extends TestCase
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

    public function test_user_can_create_budget(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        $payload = [
            'category_id' => $category->id,
            'amount' => 500.00,
            'month' => 7,
            'year' => 2025,
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/v1/budgets', $payload);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'category', 'amount', 'month', 'year', 'created_at', 'updated_at'
                ]
            ]);
    }

    public function test_user_can_list_budgets(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        \App\Models\Budget::factory()->count(2)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/v1/budgets');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'category', 'amount', 'month', 'year', 'created_at', 'updated_at'
                    ]
                ]
            ]);
    }

    public function test_user_can_view_single_budget(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        $budget = \App\Models\Budget::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/v1/budgets/' . $budget->id);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'category', 'amount', 'month', 'year', 'created_at', 'updated_at'
                ]
            ]);
    }

    public function test_user_can_update_budget(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        $budget = \App\Models\Budget::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $payload = [
            'category_id' => $category->id,
            'amount' => 1000.00,
            'month' => 8,
            'year' => 2025,
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/v1/budgets/' . $budget->id, $payload);
        $response->assertStatus(200)
            ->assertJson(['data' => ['amount' => 1000.00, 'month' => 8, 'year' => 2025]]);
    }

    public function test_user_can_delete_budget(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        $budget = \App\Models\Budget::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/v1/budgets/' . $budget->id);
        $response->assertStatus(204);
    }

    public function test_user_can_filter_budgets_by_month_and_year(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        \App\Models\Budget::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 7,
            'year' => 2025,
        ]);
        \App\Models\Budget::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 8,
            'year' => 2025,
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/v1/budgets?month=7&year=2025');
        $response->assertStatus(200)
            ->assertJsonFragment(['month' => 7, 'year' => 2025]);
    }
}
