<?php

namespace App\Http\Resources\Learning\Schedule;

use App\Http\Resources\Academic\Classroom\ClassRoomResource;
use App\Http\Resources\Academic\Subject\SubjectResource;
use App\Http\Resources\Account\User\UserResource;
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
            'room_type' => $this->room_type,
            'meeting_link' => $this->meeting_link,
            'scheduled_at' => $this->scheduled_at,
            'duration_minutes' => $this->duration_minutes,

            'instructor' => UserResource::make($this->whenLoaded('instructor')),
            'classroom' => ClassRoomResource::make($this->whenLoaded('classroom')),
            'subject' => SubjectResource::make($this->whenLoaded('subject')),
        ];
    }
}
