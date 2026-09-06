<?php

namespace App\Http\Requests\Academic\Program;

use App\Models\Academic\Program;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
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
        $program = $this->route('program');
        $programId = $program instanceof Program ? $program->id : $program;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('programs', 'code')->ignore($programId),
            ],
            'description' => ['nullable', 'string'],
        ];
    }
}
