<?php

namespace App\Http\Requests\Learning\Schedule;

use App\Enum\Access\RoleRegistryEnum;
use App\Enum\Learning\RoomTypeEnum;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreClassScheduleRequest extends FormRequest
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
            'classroom_id' => ['required', 'integer', 'exists:classrooms,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'instructor_id' => ['required', 'integer', 'exists:users,id'],
            'room_type' => ['required', Rule::enum(RoomTypeEnum::class)],
            'meeting_link' => ['nullable', 'string', 'url', 'required_if:room_type,online'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
        ];
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $instructorId = $this->input('instructor_id');
                if ($instructorId) {
                    /** @var User|null $instructor */
                    $instructor = User::find($instructorId);
                    if ($instructor && !$instructor->hasAnyRole([
                        RoleRegistryEnum::INSTRUCTOR->value,
                        RoleRegistryEnum::SUPERADMIN->value
                    ])) {
                        $validator->errors()->add('instructor_id', 'user yang dipilih bukan seorang instruktur.');
                    }
                }
            },
        ];
    }
}
