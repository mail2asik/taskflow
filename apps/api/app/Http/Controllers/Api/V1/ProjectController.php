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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $projects = Project::query()
            ->where('owner_id', $request->user()->id)
            ->orWhereHas('members', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['owner', 'members', 'labels'])
            ->latest()
            ->get();

        return $this->successResponse(
            ProjectResource::collection($projects),
            'Projects retrieved successfully.'
        );
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = DB::transaction(function () use ($request) {
            $project = Project::create([
                ...$request->validated(),
                'owner_id' => $request->user()->id,
            ]);

            // Always attach creator as owner
            $members = [
                $request->user()->id => ['role' => 'owner']
            ];

            // Add additional selected members
            if ($request->filled('members')) {
                foreach ($request->input('members') as $member) {
                    if ($member['user_id'] !== $request->user()->id) {
                        $members[$member['user_id']] = ['role' => $member['role'] ?? 'member'];
                    }
                }
            }
            $project->members()->attach($members);

            // Seed initial labels (from input or config defaults)
            $labelsInput = $request->input('labels', config('taskflow.default_labels'));
            $labels = collect($labelsInput)->map(fn ($label) => [
                'name' => $label['name'],
                'color_code' => $label['color'] ?? $label['color_code'],
            ]);
            $project->labels()->createMany($labels->toArray());

            return $project;
        });

        return $this->successResponse(
            new ProjectResource($project->load(['owner', 'members', 'labels'])),
            'Project created successfully.',
            201
        );
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('update', $project);

        DB::transaction(function () use ($request, $project) {
            $project->update($request->validated());

            // Sync Labels if explicit array passed
            if ($request->has('labels')) {
                $project->labels()->delete();
                $labels = collect($request->input('labels'))->map(fn ($label) => [
                    'name' => $label['name'],
                    'color_code' => $label['color'] ?? $label['color_code'],
                ]);
                $project->labels()->createMany($labels->toArray());
            }

            // Sync Members if explicit array passed
            if ($request->has('members')) {
                $members = [
                    $project->owner_id => ['role' => 'owner']
                ];

                foreach ($request->input('members') as $member) {
                    if ((int)$member['user_id'] !== (int)$project->owner_id) {
                        $members[$member['user_id']] = ['role' => $member['role'] ?? 'member'];
                    }
                }
                $project->members()->sync($members);
            }
        });

        return $this->successResponse(
            new ProjectResource($project->load(['owner', 'members', 'labels'])),
            'Project updated successfully.'
        );
    }

    public function show(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return $this->successResponse(
            new ProjectResource($project->load(['owner', 'members', 'labels'])),
            'Project details retrieved successfully.'
        );
    }

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