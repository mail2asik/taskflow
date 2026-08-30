<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Issue\StoreIssueRequest;
use App\Http\Requests\V1\Issue\UpdateIssueRequest;
use App\Http\Resources\V1\IssueResource;
use App\Events\IssueUpdated;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IssueController extends Controller
{
    public function index(Request $request, Project $project): AnonymousResourceCollection
    {
        $query = $project->issues()->with(['assignee', 'reporter', 'labels']);

        // Search filter by title or issue key
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('issue_key', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Priority filter
        if ($priority = $request->query('priority')) {
            $query->where('priority', $priority);
        }

        // Assignee filter
        if ($assigneeId = $request->query('assignee_id')) {
            $query->where('assignee_id', $assigneeId);
        }

        return IssueResource::collection($query->latest()->get());
    }

    public function store(StoreIssueRequest $request, Project $project): JsonResponse
    {
        // Generate issue key like OP-1, OP-2 based on project key and count
        $nextNumber = $project->issues()->count() + 1;
        $issueKey = "{$project->key}-{$nextNumber}";

        $issue = $project->issues()->create([
            ...$request->validated(),
            'issue_key' => $issueKey,
            'reporter_id' => $request->user()->id,
        ]);

        if ($request->has('label_ids')) {
            $issue->labels()->sync($request->label_ids);
        }

        return response()->json(
            new IssueResource($issue->load(['assignee', 'reporter', 'labels'])),
            201
        );
    }

    public function show(Issue $issue): IssueResource
    {
        return new IssueResource($issue->load(['assignee', 'reporter', 'labels']));
    }

    public function update(UpdateIssueRequest $request, Issue $issue): IssueResource
    {
        $issue->update($request->validated());

        if ($request->has('label_ids')) {
            $issue->labels()->sync($request->label_ids);
        }

        $issue->load(['assignee', 'reporter', 'labels']);

        // Broadcast update across WebSocket channel
        broadcast(new IssueUpdated($issue))->toOthers();

        return new IssueResource($issue);
    }

    public function destroy(Issue $issue): JsonResponse
    {
        $issue->delete();

        return response()->json(['message' => 'Issue deleted successfully.']);
    }
}