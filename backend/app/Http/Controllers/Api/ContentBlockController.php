<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContentBlock;

class ContentBlockController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_id' => 'required|exists:pages,id',
            'type' => 'required|string|max:255',
            'content' => 'required',
            'title' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);
        $block = ContentBlock::create($validated);
        return $block;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $block = ContentBlock::findOrFail($id);
        $validated = $request->validate([
            'type' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required',
            'title' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);
        $block->update($validated);
        return $block;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $block = ContentBlock::findOrFail($id);
        $block->delete();
        return response()->noContent();
    }
}
