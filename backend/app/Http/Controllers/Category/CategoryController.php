<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::query()
            ->where(function ($q) use ($request) {
                $q->whereNull('user_id');
                if ($request->user()) {
                    $q->orWhere('user_id', $request->user()->id);
                }
            })
            ->orderBy('name')
            ->get();
        return makeApiResponse(CategoryResource::collection($categories));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color,
            'user_id' => $request->user()->id,
        ]);
        return makeApiResponse(new CategoryResource($category), 'Category created.', true, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $category = Category::where(function ($q) use ($request) {
                $q->whereNull('user_id');
                if ($request->user()) {
                    $q->orWhere('user_id', $request->user()->id);
                }
            })
            ->findOrFail($id);
        return makeApiResponse(new CategoryResource($category));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::where('user_id', $request->user()->id)->findOrFail($id);
        $category->update($request->validated());
        return makeApiResponse(new CategoryResource($category), 'Category updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $category = Category::where('user_id', $request->user()->id)->findOrFail($id);
        $category->delete();
        return response()->noContent();
    }
}
