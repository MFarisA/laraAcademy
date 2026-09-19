<?php

namespace App\Http\Requests\Learning\Attendance;

use App\Enum\Learning\AttendanceStatusEnum;
use App\Models\Learning\ClassSchedule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    #[\Override]
    protected function prepareForValidation(): void
    {
        $schedule = $this->route('schedule');
        $this->merge([
            'class_schedule_id' => $schedule instanceof ClassSchedule
                ? $schedule->id
                : $schedule,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'class_schedule_id' => ['required', 'integer', 'exists:class_schedules,id'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'attendances.*.status' => ['required', Rule::enum(AttendanceStatusEnum::class)],
        ];
    }
}
