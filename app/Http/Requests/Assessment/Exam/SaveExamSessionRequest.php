<?php

namespace App\Http\Requests\Assessment\Exam;

use Illuminate\Foundation\Http\FormRequest;

class SaveExamSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'exam_template_id' => ['required', 'integer', 'exists:exam_templates,id'],
            'classroom_id' => ['nullable', 'integer', 'exists:classrooms,id'],
            'title' => ['required', 'string', 'max:50'],
            'token' => ['nullable', 'string', 'max:10'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ];
    }
}
