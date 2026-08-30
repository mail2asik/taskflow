<?php

namespace App\Http\Requests\V1\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'key' => ['sometimes', 'required', 'string', 'max:10', 'uppercase', 'unique:projects,key,' . $this->route('project')->id],
            'labels' => ['nullable', 'array'],
            'labels.*.name' => ['required', 'string', 'max:50'],
            'labels.*.color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_archived' => ['sometimes', 'boolean'],
        ];
    }
}