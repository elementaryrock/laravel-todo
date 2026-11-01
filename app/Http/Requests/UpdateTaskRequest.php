<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'notes' => ['nullable', 'string', 'max:1200'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITIES))],
            'time_window' => ['required', Rule::in(array_keys(Task::TIME_WINDOWS))],
            'due_date' => ['nullable', 'date'],
            'estimated_minutes' => ['required', 'integer', 'min:5', 'max:480'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A task title is required to save changes.',
            'title.max' => 'Keep the task title under 180 characters; place longer context in notes.',
            'estimated_minutes.min' => 'Set an estimated duration of at least 5 minutes.',
            'estimated_minutes.max' => 'Single-task duration cannot exceed 480 minutes (8 hours); split larger blocks.',
        ];
    }
}

