<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Project\StoreProjectRequest;
use App\Http\Requests\V1\Project\UpdateProjectRequest;
use App\Http\Resources\V1\ProjectResource;
use App\Models\Project;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of projects accessible by the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $projects = Project::query()
            ->where('owner_id', $request->user()->id)
            ->orWhereHas('members', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['owner', 'members'])
            ->latest()
            ->get();

        return $this->successResponse(
            ProjectResource::collection($projects),
            'Projects retrieved successfully.'
        );
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create([
            ...$request->validated(),
            'owner_id' => $request->user()->id,
        ]);

        // Attach owner as project member
        $project->members()->attach($request->user()->id, ['role' => 'owner']);

        // Add labels to the project
        if ($request->has('labels')) {
            $labels = collect($request->input('labels'))->map(function ($label) {
                return ['name' => $label['name'], 'color' => $label['color']];
            });
            $project->labels()->createMany($labels);
        }

        // Add members to the project
        if ($request->has('members')) {
            $members = collect($request->input('members'))->mapWithKeys(function ($member) {
                return [$member['user_id'] => ['role' => $member['role']]];
            });
            $project->members()->attach($members);
        }

        return $this->successResponse(
            new ProjectResource($project->load(['owner', 'members'])),
            'Project created successfully.',
            201
        );
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->successResponse(
            new ProjectResource($project->load(['owner', 'members'])),
            'Project details retrieved successfully.'
        );
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('update', $project);

        $project->update($request->validated());

        // Update labels if provided
        if ($request->has('labels')) {
            $project->labels()->delete(); // Remove existing labels
            $labels = collect($request->input('labels'))->map(function ($label) {
                return ['name' => $label['name'], 'color' => $label['color']];
            });
            $project->labels()->createMany($labels);
        }

        // Update members if provided
        if ($request->has('members')) {
            $project->members()->detach(); // Remove existing members
            $members = collect($request->input('members'))->mapWithKeys(function ($member) {
                return [$member['user_id'] => ['role' => $member['role']]];
            });
            $project->members()->attach($members);
        }

        return $this->successResponse(
            new ProjectResource($project->load(['owner', 'members'])),
            'Project updated successfully.'
        );
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return $this->successResponse(
            null,
            'Project deleted successfully.'
        );
    }
}