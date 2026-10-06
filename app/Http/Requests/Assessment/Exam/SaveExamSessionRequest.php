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
            // TODO: Tentukan aturan validasi sesi ujian (template_id, jadwal start & end, token)
        ];
    }
}
