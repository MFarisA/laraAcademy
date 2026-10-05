<?php

namespace App\Http\Requests\Assessment\Question;

use App\Enum\Assessment\DifficultyLevelEnum;
use App\Enum\Assessment\GradingRuleEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'difficulty_level' => ['nullable', Rule::enum(DifficultyLevelEnum::class)],
            'grading_rule' => ['nullable', Rule::enum(GradingRuleEnum::class)],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    // /**
    //  * @return array<string, string>
    //  */
    // public function messages(): array
    // {
    //     return [
    //         'subject_id.exists' => 'Mata pelajaran yang dipilih tidak ditemukan.',
    //         'difficulty_level.Illuminate\Validation\Rules\Enum' => 'Tingkat kesulitan tidak valid (harus easy, medium, atau hard).',
    //         'grading_rule.Illuminate\Validation\Rules\Enum' => 'Aturan penilaian tidak valid (harus STANDARD atau TKP).',
    //         'per_page.max' => 'Maksimal data per halaman adalah 100.',
    //     ];
    // }
}
