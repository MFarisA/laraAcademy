<?php

namespace App\Http\Resources\Learning\Attendance;

use App\Http\Resources\Account\User\UserResource;
use App\Models\Learning\Attendance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Attendance
 */
class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'class_schedule_id' => $this->class_schedule_id,
            'student_id' => $this->student_id,
            'status' => $this->status,
            'verified_at' => $this->verified_at?->toIsoString(),
            'student' => UserResource::make($this->whenLoaded('student')),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
