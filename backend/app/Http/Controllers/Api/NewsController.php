<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return News::orderByDesc('published_at')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'published_at' => 'nullable|date',
        ]);
        return News::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return News::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $news = News::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'image_url' => 'nullable|url',
            'published_at' => 'nullable|date',
        ]);
        $news->update($validated);
        return $news;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $news = News::findOrFail($id);
        $news->delete();
        return response()->noContent();
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // max 5MB
        ]);
        $file = $request->file('image');
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getPathname());
        $image->scale(width: 1500); // Resize to max 1500px width
        $filename = uniqid('news_') . '.' . $file->getClientOriginalExtension();
        $path = 'news/' . $filename;
        Storage::disk('public')->put($path, (string) $image->toJpeg());
        return response()->json(['path' => '/storage/' . $path]);
    }
}
