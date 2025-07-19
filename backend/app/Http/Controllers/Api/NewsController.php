<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Upload;

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
            'summary' => 'nullable|string|max:255',
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
            'summary' => 'nullable|string|max:255',
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
            'entity_type' => 'nullable|string',
            'entity_id' => 'nullable|integer',
        ]);
        $file = $request->file('image');
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getPathname());
        $image->scale(width: 1500); // Resize to max 1500px width
        $filename = uniqid('upload_') . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/' . $filename;
        Storage::disk('public')->put($path, (string) $image->toJpeg());
        $upload = new Upload([
            'path' => '/storage/' . $path,
            'type' => 'image',
        ]);
        if ($request->entity_type && $request->entity_id) {
            $upload->entity_type = $request->entity_type;
            $upload->entity_id = $request->entity_id;
        }
        $upload->save();
        return response()->json(['id' => $upload->id, 'path' => $upload->path]);
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);
        $relativePath = ltrim($request->input('path'), '/');
        $relativePath = preg_replace('#^storage/#', '', $relativePath); // Remove leading 'storage/'
        $upload = Upload::where('path', $request->input('path'))->first();
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
        if ($upload) {
            $upload->delete();
        }
        return response()->json(['success' => true]);
    }
}
