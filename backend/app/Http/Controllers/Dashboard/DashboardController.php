<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function overview(Request $request)
    {
        // In production, calculate real stats from transactions, budgets, etc.
        $data = [
            'income' => 5000.00,
            'expenses' => 3200.00,
            'top_categories' => [
                ['name' => 'Groceries', 'amount' => 800.00],
                ['name' => 'Rent', 'amount' => 1200.00],
                ['name' => 'Transport', 'amount' => 300.00],
            ],
            'trends' => [
                ['month' => '2025-05', 'income' => 4800, 'expenses' => 3100],
                ['month' => '2025-06', 'income' => 5000, 'expenses' => 3200],
                ['month' => '2025-07', 'income' => 5100, 'expenses' => 3300],
            ],
        ];
        return makeApiResponse($data);
    }
}
