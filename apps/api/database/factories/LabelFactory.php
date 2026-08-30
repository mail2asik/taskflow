<?php

namespace Database\Factories;

use App\Models\Label;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class LabelFactory extends Factory
{
    protected $model = Label::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => $this->faker->unique()->word(),
            'color_code' => $this->faker->safeHexColor(),
        ];
    }
}