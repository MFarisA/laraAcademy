<?php

namespace App\Http\Requests\Academic\Subject;

use App\Models\Academic\Subject;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
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
        $subject = $this->route('subject');
        $subjectId = $subject instanceof Subject ? $subject->id : $subject;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('subjects', 'code')->ignore($subjectId),
            ],
            'description' => ['nullable', 'string'],
        ];
    }
}
