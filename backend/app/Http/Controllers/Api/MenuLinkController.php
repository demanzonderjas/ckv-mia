<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuLink;

class MenuLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MenuLink::where('active', true);
        if ($request->has('category')) {
            $query->where('category', $request->query('category'));
        }
        return $query->orderBy('order')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'order' => 'integer',
            'active' => 'boolean',
            'description' => 'nullable|string',
            'category' => 'required|string|in:header,side,footer',
            'parent_id' => 'nullable|exists:menu_links,id',
        ]);
        return MenuLink::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return MenuLink::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $link = MenuLink::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|string|max:255',
            'order' => 'integer',
            'active' => 'boolean',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|string|in:header,side,footer',
            'parent_id' => 'nullable|exists:menu_links,id',
        ]);
        $link->update($validated);
        return $link;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $link = MenuLink::findOrFail($id);
        $link->delete();
        return response()->noContent();
    }
}
