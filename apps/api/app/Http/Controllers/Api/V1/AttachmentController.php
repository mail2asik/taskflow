<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AttachmentResource;
use App\Models\Attachment;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request, Issue $issue): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'], // Max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store("attachments/{$issue->id}", 'public');

        $attachment = $issue->attachments()->create([
            'user_id' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json(new AttachmentResource($attachment), 201);
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['message' => 'Attachment removed.']);
    }
}