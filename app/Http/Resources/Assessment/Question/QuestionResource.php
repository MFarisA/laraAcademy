<?php

namespace App\Http\Resources\Assessment\Question;

use App\Http\Resources\Academic\Subject\SubjectResource;
use App\Models\Assessment\Question\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Question
 */
class QuestionResource extends JsonResource
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
            'subject_id' => $this->subject_id,
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'question_text' => $this->question_text,
            'image_url' => $this->image_url,
            'grading_rule' => $this->grading_rule,
            'difficulty_level' => $this->difficulty_level,
            'options' => QuestionOptionResource::collection($this->whenLoaded('options')),
            'created_at' => $this->created_at?->toIsoString(),
            'updated_at' => $this->updated_at?->toIsoString(),
        ];
    }
}
