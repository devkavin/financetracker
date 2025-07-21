<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Groceries', 'type' => 'expense', 'color' => '#4caf50'],
            ['name' => 'Rent', 'type' => 'expense', 'color' => '#f44336'],
            ['name' => 'Utilities', 'type' => 'expense', 'color' => '#2196f3'],
            ['name' => 'Transport', 'type' => 'expense', 'color' => '#ff9800'],
            ['name' => 'Dining Out', 'type' => 'expense', 'color' => '#9c27b0'],
            ['name' => 'Entertainment', 'type' => 'expense', 'color' => '#e91e63'],
            ['name' => 'Healthcare', 'type' => 'expense', 'color' => '#00bcd4'],
            ['name' => 'Salary', 'type' => 'income', 'color' => '#8bc34a'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#cddc39'],
            ['name' => 'Investments', 'type' => 'income', 'color' => '#ffc107'],
            ['name' => 'Other', 'type' => 'both', 'color' => '#607d8b'],
        ];
        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
