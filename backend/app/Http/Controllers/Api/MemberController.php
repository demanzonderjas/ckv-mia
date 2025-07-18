<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::query();
        if ($request->has('team_id')) {
            $query->where('team_id', $request->query('team_id'));
        }
        $members = $query->orderBy('last_name')->orderBy('first_name')->get();

        // Find duplicate first names in the whole club
        $allFirstNames = Member::select('first_name')->get()->groupBy('first_name');
        $duplicates = $allFirstNames->filter(function ($group) {
            return $group->count() > 1;
        })->keys();

        // Map to privacy-safe names
        $result = $members->map(function ($member) use ($duplicates) {
            $name = $member->first_name;
            if ($duplicates->contains($member->first_name)) {
                $name .= ' ' . strtoupper(substr($member->last_name, 0, 1));
            }
            return [
                'id' => $member->id,
                'name' => $name,
            ];
        });
        return $result->values();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'nullable|string|max:255',
            'team_id' => 'required|exists:teams,id',
        ]);
        return Member::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Member::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:members,email,' . $id,
            'phone' => 'nullable|string|max:255',
            'team_id' => 'sometimes|required|exists:teams,id',
        ]);
        $member->update($validated);
        return $member;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();
        return response()->noContent();
    }
}
