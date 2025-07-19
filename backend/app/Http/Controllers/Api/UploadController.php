<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Upload;

class UploadController extends Controller
{
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
