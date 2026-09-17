<?php

namespace App\Http\Resources\Learning\Schedule;

use App\Http\Resources\Academic\ClassRoomResource;
use App\Http\Resources\Academic\SubjectResource;
use App\Models\Learning\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ClassSchedule
 */
class ClassScheduleResource extends JsonResource
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
            'classroom_id' => $this->classroom_id,
            'subject_id' => $this->subject_id,
            'instructor_id' => $this->instructor_id,
            'room_type' => $this->room_type,
            'meeting_link' => $this->meeting_link,
            'scheduled_at' => $this->scheduled_at?->toIsoString(),
            'duration_minutes' => $this->duration_minutes,
            'classroom' => ClassRoomResource::make($this->whenLoaded('classroom')),
            'subject' => SubjectResource::make($this->whenLoaded('subject')),
            'instructor' => $this->whenLoaded('instructor', fn() => [
                'id' => $this->instructor->id,
                'name' => $this->instructor->name,
                'email' => $this->instructor->email,
            ]),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
