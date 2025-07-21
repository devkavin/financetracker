<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'category_id' => \App\Models\Category::factory(),
            'user_id' => \App\Models\User::factory(),
            'description' => $this->faker->sentence(),
            'date' => $this->faker->date(),
        ];
    }
}
