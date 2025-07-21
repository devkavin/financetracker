<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Budget\StoreBudgetRequest;
use App\Http\Requests\Budget\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Budget::with('category')->where('user_id', $request->user()->id);
        if ($month = $request->query('month')) {
            $query->where('month', $month);
        }
        if ($year = $request->query('year')) {
            $query->where('year', $year);
        }
        $budgets = $query->orderByDesc('year')->orderByDesc('month')->get();
        return makeApiResponse(BudgetResource::collection($budgets));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBudgetRequest $request)
    {
        $budget = Budget::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'month' => $request->month,
            'year' => $request->year,
        ]);
        $budget->load('category');
        return makeApiResponse(new BudgetResource($budget), 'Budget created.', true, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $budget = Budget::with('category')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);
        return makeApiResponse(new BudgetResource($budget));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBudgetRequest $request, $id)
    {
        $budget = Budget::where('user_id', $request->user()->id)->findOrFail($id);
        $budget->update($request->validated());
        $budget->load('category');
        return makeApiResponse(new BudgetResource($budget), 'Budget updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $budget = Budget::where('user_id', $request->user()->id)->findOrFail($id);
        $budget->delete();
        return response()->noContent();
    }
}
