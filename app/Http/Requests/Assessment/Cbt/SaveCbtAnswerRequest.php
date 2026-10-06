<?php

namespace App\Http\Requests\Assessment\Cbt;

use Illuminate\Foundation\Http\FormRequest;

class SaveCbtAnswerRequest extends FormRequest
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
            // TODO: Validasi question_id, selected_option_id, time_spent_seconds
        ];
    }
}
