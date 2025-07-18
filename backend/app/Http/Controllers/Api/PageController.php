<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Page::orderBy('title')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug',
            'description' => 'nullable|string',
        ]);
        return Page::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Page::with('contentBlocks')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $page = Page::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:pages,slug,' . $id,
            'description' => 'nullable|string',
        ]);
        $page->update($validated);
        return $page;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $page = Page::findOrFail($id);
        $page->delete();
        return response()->noContent();
    }

    public function showBySlug($slug)
    {
        $page = Page::with('contentBlocks')->where('slug', $slug)->firstOrFail();
        return $page;
    }
}
