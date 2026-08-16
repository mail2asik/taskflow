<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Project\StoreProjectRequest;
use App\Http\Requests\V1\Project\UpdateProjectRequest;
use App\Http\Resources\V1\ProjectResource;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = Project::query()
            ->where('owner_id', $request->user()->id)
            ->orWhereHas('members', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['owner', 'members'])
            ->latest()
            ->get();

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create([
            ...$request->validated(),
            'owner_id' => $request->user()->id,
        ]);

        // Attach owner as project member
        $project->members()->attach($request->user()->id, ['role' => 'owner']);

        return response()->json(new ProjectResource($project->load(['owner', 'members'])), 201);
    }

    public function show(Project $project): ProjectResource
    {
        Gate::authorize('view', $project);

        return new ProjectResource($project->load(['owner', 'members']));
    }

    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        Gate::authorize('update', $project);

        $project->update($request->validated());

        return new ProjectResource($project->load(['owner', 'members']));
    }

    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully.']);
    }
}