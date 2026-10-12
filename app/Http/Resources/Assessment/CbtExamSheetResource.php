<?php

namespace App\Http\Resources\Assessment;

use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ExamAttempt
 */
class CbtExamSheetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $session = $this->session;
        $template = $session?->template;
        $saveAnswer = $this->answers()->pluck('selected_option_id', 'question_id');

        return [
            'attempt_id' => $this->id,
            'status' => $this->status,
            'started_at' => $this->started_at?->toIsoString(),
            'total_duration_minutes' => $template->total_duration_minutes,
            'session' => [
                'id' => $session?->id,
                'title' => $session?->title,
            ],
            'sections' => $template?->sections?->map(function ($section) use ($saveAnswer) {
                return [
                    'id' => $section->id,
                    'title' => $section->title,
                    'duration_minutes' => $section->duration_minutes,
                    'order_index' => $section->order_index,
                    'questions' => $section->questions->map(function (Question $question) use ($saveAnswer) {
                        return [
                            'id' => $question->id,
                            'question_text' => $question->question_text,
                            'image_url' => $question->image_url,
                            'difficulty_level' => $question->difficulty_level,
                            'selected_option_id' => $saveAnswer[$question->id] ?? null,
                            'options' => $question->options->map(function (QuestionOption $option) {
                                return [
                                    'id' => $option->id,
                                    'option_label' => $option->option_label,
                                    'option_text' => $option->option_text,
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ];
    }
}
