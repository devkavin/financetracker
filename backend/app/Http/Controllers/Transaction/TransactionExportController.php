<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionExportController extends Controller
{
    public function export(Request $request)
    {
        $query = \App\Models\Transaction::with('category')->where('user_id', $request->user()->id);
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        if ($category = $request->query('category_id')) {
            $query->where('category_id', $category);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('date', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('date', '<=', $to);
        }
        $transactions = $query->orderByDesc('date')->get();
        \Log::info($transactions);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=transactions.csv',
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['amount', 'type', 'category', 'description', 'date']);
            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->amount,
                    $t->type,
                    $t->category ? $t->category->name : '',
                    $t->description,
                    $t->date,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
