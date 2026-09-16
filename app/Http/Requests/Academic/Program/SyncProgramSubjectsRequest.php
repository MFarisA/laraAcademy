<?php

namespace App\Http\Requests\Academic\Program;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SyncProgramSubjectsRequest extends FormRequest
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
            'subjects' => ['present', 'array'],
            'subjects.*.id' => ['required', 'integer', 'exists:subjects,id'],
            'subjects.*.min_passing_score' => ['required', 'numeric', 'min:0'],
        ];
    }
}
