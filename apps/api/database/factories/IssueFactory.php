<?php

namespace Database\Factories;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    protected $model = Issue::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'reporter_id' => User::factory(),
            'assignee_id' => User::factory(),
            'issue_key' => 'TEMP-0', // Will be calculated dynamically in seeder
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(IssueStatus::cases()),
            'priority' => $this->faker->randomElement(IssuePriority::cases()),
            'due_date' => $this->faker->optional(0.7)->dateTimeBetween('now', '+1 month'),
        ];
    }
}