<?php

namespace App\Http\Requests\V1\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:10', 'uppercase', 'unique:projects,key'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}