<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Requests\Transaction\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('category')->where('user_id', $request->user()->id);
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
        return makeApiResponse(TransactionResource::collection($transactions));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'date' => $request->date,
        ]);
        $transaction->load('category');
        return makeApiResponse(new TransactionResource($transaction), 'Transaction created.', true, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $transaction = Transaction::with('category')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);
        return makeApiResponse(new TransactionResource($transaction));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);
        $transaction->update($request->validated());
        $transaction->load('category');
        return makeApiResponse(new TransactionResource($transaction), 'Transaction updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);
        $transaction->delete();
        return response()->noContent();
    }
}
