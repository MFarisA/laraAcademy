<?php

namespace App\Http\Requests\Learning\LearningMaterial;

use App\Models\Learning\LearningMaterial;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StreamLearningMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 1. Validasi Signed URL
        if (! $this->hasValidSignature()) {
            return false;
        }

        /** @var LearningMaterial $material */
        $material = $this->route('material');

        // 2. Verifikasi hak akses view (enrollment)
        if ($this->user()?->cannot('view', $material)) {
            return false;
        }

        // 3. Verifikasi hak unduh jika parameter download diaktifkan
        if ($this->boolean('download') && $this->user()?->cannot('download', $material)) {
            return false;
        }

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
            'download' => ['sometimes', 'boolean'],
        ];
    }
}
