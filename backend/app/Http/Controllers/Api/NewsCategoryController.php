<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsCategoryController extends Controller
{
    public function index()
    {
        return NewsCategory::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:news_categories,slug',
        ]);
        return NewsCategory::create($validated);
    }

    public function show($id)
    {
        return NewsCategory::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $category = NewsCategory::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:news_categories,slug,' . $id,
        ]);
        $category->update($validated);
        return $category;
    }

    public function destroy($id)
    {
        $category = NewsCategory::findOrFail($id);
        $category->delete();
        return response()->noContent();
    }
}
