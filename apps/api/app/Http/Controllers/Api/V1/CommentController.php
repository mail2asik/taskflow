<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Comment\StoreCommentRequest;
use App\Http\Resources\V1\CommentResource;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Issue $issue): AnonymousResourceCollection
    {
        return CommentResource::collection(
            $issue->comments()->with('user')->latest()->get()
        );
    }

    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        $comment = $issue->comments()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        return response()->json(
            new CommentResource($comment->load('user')),
            201
        );
    }

    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }
}