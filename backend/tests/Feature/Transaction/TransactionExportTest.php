<?php

namespace Tests\Feature\Transaction;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionExportTest extends TestCase
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

    public function test_user_can_export_transactions_as_csv(): void
    {
        [$user, $token] = $this->authenticate();
        $category = \App\Models\Category::factory()->create();
        \App\Models\Transaction::factory()->count(2)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeaders(['Accept' => 'application/json'])
            ->get('/v1/transactions/export?type=expense');
        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertHeader('Content-Disposition', 'attachment; filename=transactions.csv');
        $response->assertStreamed();
        ob_start();
        $response->sendContent();
        $output = ob_get_clean();
        $this->assertStringContainsString('amount,type,category,description,date', $output);
    }

    public function test_guest_cannot_export_transactions(): void
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->get('/v1/transactions/export');
        $response->assertStatus(401);
    }
}
