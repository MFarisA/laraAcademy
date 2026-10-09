<?php

namespace App\Http\Resources\Assessment;

use App\Http\Resources\Academic\Classroom\ClassRoomResource;
use App\Models\Assessment\Exam\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ExamSession
 */
class ExamSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'exam_template_id' => $this->exam_template_id,
            'title' => $this->title,
            'token' => $this->token,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_public' => $this->classroom_id === null,
            'template' => $this->whenLoaded('template', function () {
                return [
                    'id' => $this->template->id,
                    'title' => $this->template->title,
                    'type' => $this->template->type,
                    'total_duration_minutes' => $this->template->total_duration_minutes,
                ];
            }),
            'classroom' => ClassRoomResource::make($this->whenLoaded('classroom')),
            'attempts_count' => $this->whenCounted('attempts'),
            'created_at' => $this->created_at?->toIsoString(),
        ];
    }
}
