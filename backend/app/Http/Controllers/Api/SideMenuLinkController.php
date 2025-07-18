<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SideMenuLink;

class SideMenuLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SideMenuLink::where('active', true)->orderBy('order')->get();
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
        ]);
        return SideMenuLink::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return SideMenuLink::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $link = SideMenuLink::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'url' => 'sometimes|required|string|max:255',
            'order' => 'integer',
            'active' => 'boolean',
            'description' => 'nullable|string',
        ]);
        $link->update($validated);
        return $link;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $link = SideMenuLink::findOrFail($id);
        $link->delete();
        return response()->noContent();
    }
}
