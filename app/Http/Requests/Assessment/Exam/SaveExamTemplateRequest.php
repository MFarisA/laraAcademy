<?php

namespace App\Http\Requests\Assessment\Exam;

use App\Enum\Assessment\ExamTemplateTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveExamTemplateRequest extends FormRequest
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
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'title' => ['required', 'string', 'min:5', 'max:50'],
            'type' => ['required', Rule::enum(ExamTemplateTypeEnum::class)],
            'total_duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'sections' => ['nullable', 'array'],
            'sections.*.subject_id' => ['required_with:sections', 'integer', 'exists:subjects,id'],
            'sections.*.title' => ['required_with:sections', 'string', 'max:255'],
            'sections.*.passing_grade' => ['required_with:sections', 'numeric', 'min:0'],
            'sections.*.duration_minutes' => ['required_with:sections', 'integer', 'min:1'],
            'sections.*.order_index' => ['required_with:sections', 'integer', 'min:1'],
        ];
    }
}
