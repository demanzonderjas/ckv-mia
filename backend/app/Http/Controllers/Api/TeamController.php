<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Team::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:wedstrijdkorfbal,breedtekorfbal,midweek',
            'type' => 'required|string|in:senior,junior',
        ]);
        return Team::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Team::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $team = Team::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|required|string|in:wedstrijdkorfbal,breedtekorfbal,midweek',
            'type' => 'sometimes|required|string|in:senior,junior',
        ]);
        $team->update($validated);
        return $team;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $team = Team::findOrFail($id);
        $team->delete();
        return response()->noContent();
    }
}
