<?php

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;

test('authenticated user can create an issue in a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson("/api/v1/projects/{$project->id}/issues", [
            'title' => 'Fix database indexing bug',
            'priority' => 'high',
            'status' => 'todo',
        ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'Fix database indexing bug')
        ->assertJsonPath('data.issue_key', "{$project->key}-1");

    $this->assertDatabaseHas('issues', [
        'title' => 'Fix database indexing bug',
        'project_id' => $project->id,
    ]);
});

test('user can update issue status on kanban board', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);
    $issue = Issue::factory()->create([
        'project_id' => $project->id,
        'reporter_id' => $user->id,
        'status' => 'todo',
    ]);

    $response = $this->actingAs($user)
        ->patchJson("/api/v1/issues/{$issue->id}", [
            'status' => 'in_progress',
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'in_progress');

    $this->assertDatabaseHas('issues', [
        'id' => $issue->id,
        'status' => 'in_progress',
    ]);
});