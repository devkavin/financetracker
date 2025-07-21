<?php

namespace Tests\Feature\Transaction;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
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

    public function test_user_can_create_transaction(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create(['type' => 'expense']);
        $payload = [
            'amount' => 100.50,
            'type' => 'expense',
            'category_id' => $category->id,
            'description' => 'Groceries',
            'date' => now()->toDateString(),
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/transactions', $payload);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'amount', 'type', 'category', 'description', 'date', 'created_at', 'updated_at'
                ]
            ]);
    }

    public function test_user_can_list_transactions(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create(['type' => 'income']);
        \App\Models\Transaction::factory()->count(3)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/transactions');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'amount', 'type', 'category', 'description', 'date', 'created_at', 'updated_at'
                    ]
                ]
            ]);
    }

    public function test_user_can_view_single_transaction(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        $transaction = \App\Models\Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'amount', 'type', 'category', 'description', 'date', 'created_at', 'updated_at'
                ]
            ]);
    }

    public function test_user_can_update_transaction(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        $transaction = \App\Models\Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $payload = [
            'amount' => 200.00,
            'type' => $transaction->type,
            'category_id' => $category->id,
            'description' => 'Updated',
            'date' => now()->toDateString(),
        ];
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/transactions/' . $transaction->id, $payload);
        $response->assertStatus(200)
            ->assertJson(['data' => ['amount' => 200.00, 'description' => 'Updated']]);
    }

    public function test_user_can_delete_transaction(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        $transaction = \App\Models\Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(204);
    }

    public function test_user_can_filter_transactions_by_type(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        \App\Models\Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'income',
        ]);
        \App\Models\Transaction::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/transactions?type=income');
        $response->assertStatus(200)
            ->assertJsonFragment(['type' => 'income']);
    }
}
