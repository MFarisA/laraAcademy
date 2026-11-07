<?php

namespace App\Http\Requests\Assessment\Physical;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePhysicalAssessmentRequest extends FormRequest
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
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'assessment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'metrics' => ['required', 'array'],
            'metrics.*' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Pilih siswa yang akan dinilai.',
            'student_id.exists' => 'Siswa yang dipilih tidak terdaftar di sistem.',
            'assessment_date.required' => 'Tanggal penilaian wajib diisi.',
            'metrics.required' => 'Isikan nilai untuk item tes fisik.',
            'metrics.*.required' => 'Nilai hasil tes wajib diisi angka.',
            'metrics.*.numeric' => 'Nilai hasil tes harus berupa angka.',
            'metrics.*.min' => 'Nilai hasil tes tidak boleh negatif.',
        ];
    }
}
