<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AttachmentResource;
use App\Models\Attachment;
use App\Models\Issue;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    use ApiResponse;

    /**
     * Store a newly uploaded attachment for an issue.
     *
     * @param Request $request
     * @param Issue $issue
     * @return JsonResponse
     */
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

        return $this->successResponse(
            new AttachmentResource($attachment),
            'Attachment uploaded successfully.',
            201
        );
    }

    /**
     * Remove the specified attachment from storage and database.
     *
     * @param Attachment $attachment
     * @return JsonResponse
     */
    public function destroy(Attachment $attachment): JsonResponse
    {
        // Delete file from the public storage disk
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        // Delete database record
        $attachment->delete();

        return $this->successResponse(
            null,
            'Attachment removed successfully.'
        );
    }
}