<?php

namespace App\Http\Controllers\Insights;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InsightsController extends Controller
{
    public function index(Request $request)
    {
        // In production, generate real insights using AI/ML or analytics
        $data = [
            'summary' => 'You spent more on groceries this month than last month. Your income is stable.',
            'anomalies' => [
                'Spike in dining out expenses on July 10th.',
                'Unusual drop in transport expenses in June.',
            ],
            'suggestions' => [
                'Consider reducing dining out to save $100/month.',
                'You could save on utilities by switching providers.',
            ],
        ];
        return makeApiResponse($data);
    }
}
