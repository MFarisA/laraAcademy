<?php

namespace App\Http\Resources\Academic\Question;

use App\Models\Assessment\Question\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin QuestionOption
 */
class QuestionOptionResource extends JsonResource
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
            'question_id' => $this->question_id,
            'option_label' => $this->option_label,
            'option_text' => $this->option_text,
            'is_correct' => $this->is_correct,
            'weight_score' => $this->weight_score,
        ];
    }
}
