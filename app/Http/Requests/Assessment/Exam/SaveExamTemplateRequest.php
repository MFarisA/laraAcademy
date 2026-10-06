<?php

namespace App\Http\Requests\Assessment\Exam;

use Illuminate\Foundation\Http\FormRequest;

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
            // TODO: Tentukan aturan validasi template, sections, dan soal
        ];
    }
}
