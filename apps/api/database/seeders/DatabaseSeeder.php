<?php

namespace Database\Seeders;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\Label;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Default Admin/Test User
        $testUser = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'admin@taskflow.dev',
            'password' => Hash::make('password'),
        ]);

        // Create Team Members
        $teamMembers = User::factory(5)->create();
        $allUsers = $teamMembers->push($testUser);

        // Define Projects
        $projectsData = [
            ['name' => 'OnePlatform Core', 'key' => 'OP', 'description' => 'Main SaaS platform development'],
            ['name' => 'Mobile App', 'key' => 'MOB', 'description' => 'Cross-platform mobile client'],
        ];

        $defaultLabelTemplates = [
            ['name' => 'Bug', 'color_code' => '#EF4444'],
            ['name' => 'Feature', 'color_code' => '#3B82F6'],
            ['name' => 'Enhancement', 'color_code' => '#10B981'],
            ['name' => 'UI/UX', 'color_code' => '#8B5CF6'],
            ['name' => 'Documentation', 'color_code' => '#F59E0B'],
        ];

        foreach ($projectsData as $pData) {
            $project = Project::create([
                'name' => $pData['name'],
                'key' => $pData['key'],
                'description' => $pData['description'],
                'owner_id' => $testUser->id,
            ]);

            // Attach team members to project
            $project->members()->attach($allUsers->pluck('id'), ['role' => 'member']);

            // Create Project-Specific Labels
            $projectLabels = collect($defaultLabelTemplates)->map(function ($tpl) use ($project) {
                return Label::create([
                    'project_id' => $project->id,
                    'name' => $tpl['name'],
                    'color_code' => $tpl['color_code'],
                ]);
            });

            // Populate Issues for project
            for ($i = 1; $i <= 10; $i++) {
                $issue = Issue::create([
                    'project_id' => $project->id,
                    'issue_key' => "{$project->key}-{$i}",
                    'title' => "Task {$i}: " . fake()->sentence(4),
                    'description' => fake()->paragraphs(2, true),
                    'status' => fake()->randomElement(IssueStatus::cases()),
                    'priority' => fake()->randomElement(IssuePriority::cases()),
                    'reporter_id' => $testUser->id,
                    'assignee_id' => $allUsers->random()->id,
                    'due_date' => fake()->optional(0.6)->dateTimeBetween('now', '+2 weeks'),
                ]);

                // Attach random project-specific labels
                $issue->labels()->attach($projectLabels->random(rand(1, 2))->pluck('id'));

                // Add random comments
                Comment::factory(rand(1, 3))->create([
                    'issue_id' => $issue->id,
                    'user_id' => $allUsers->random()->id,
                ]);

                // Add random attachments
                if (rand(0, 1)) {
                    Attachment::factory()->create([
                        'issue_id' => $issue->id,
                        'user_id' => $allUsers->random()->id,
                    ]);
                }
            }
        }
    }
}