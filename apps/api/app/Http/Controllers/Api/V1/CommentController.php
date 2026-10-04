<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Comment\StoreCommentRequest;
use App\Http\Resources\V1\CommentResource;
use App\Models\Comment;
use App\Models\Issue;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of comments for a specific issue.
     */
    public function index(Issue $issue): JsonResponse
    {
        $comments = $issue->comments()->with('user')->latest()->get();

        return $this->successResponse(
            CommentResource::collection($comments),
            'Comments retrieved successfully.'
        );
    }

    /**
     * Store a newly created comment for an issue.
     */
    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        $comment = $issue->comments()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return $this->successResponse(
            new CommentResource($comment->load('user')),
            'Comment added successfully.',
            201
        );
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return $this->successResponse(
            null,
            'Comment deleted successfully.'
        );
    }
}