<?php

namespace App\Http\Requests\Assessment\Cbt;

use Illuminate\Foundation\Http\FormRequest;

class EnterCbtSessionRequest extends FormRequest
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
            // TODO: Validasi input token sesi
        ];
    }
}
