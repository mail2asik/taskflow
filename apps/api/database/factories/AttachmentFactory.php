<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        $fileName = $this->faker->word() . '.' . $this->faker->fileExtension();

        return [
            'issue_id' => Issue::factory(),
            'user_id' => User::factory(),
            'file_name' => $fileName,
            'file_path' => "attachments/demo/{$fileName}",
            'mime_type' => 'application/octet-stream',
            'file_size' => $this->faker->numberBetween(1024, 5242880), // 1KB to 5MB
        ];
    }
}