<?php

namespace App\Http\Resources\Assessment;

use App\Models\Assessment\Exam\ExamTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ExamTemplate
 */
class ExamTemplateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'program_id' => $this->program_id,
            'title' => $this->title,
            'type' => $this->type,
            'total_duration_minutes' => $this->total_duration_minutes,
            'program' => [
                'id' => $this->program?->id,
                'name' => $this->program?->name,
            ],
            'sections' => $this->whenLoaded('sections', fn () => $this->sections->map(fn ($section) => [
                'id' => $section->id,
                'title' => $section->title,
                'passing_grade' => $section->passing_grade,
                'duration_minutes' => $section->duration_minutes,
                'order_index' => $section->order_index,
            ])),
        ];
    }
}
