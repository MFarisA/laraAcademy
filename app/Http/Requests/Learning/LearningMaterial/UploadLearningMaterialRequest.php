<?php

namespace App\Http\Requests\Learning\LearningMaterial;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadLearningMaterialRequest extends FormRequest
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
            'subject_id' => ['required', 'int', 'exists:subject,id'],
            'program_id' => ['required', 'int', 'exists:program,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:20'],
            'is_downloadable' => ['requred', 'boolean'],
            'file_url' => ['required', 'file', 'max:51200'],
        ];
    }
}
