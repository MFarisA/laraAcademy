<?php

namespace App\Http\Resources\Assessment;

use App\Models\Assessment\Exam\ExamAttempt;
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

        $sections = [];
        if ($template?->sections) {
            foreach ($template->sections as $section) {
                $questions = [];
                foreach ($section->questions as $question) {
                    $options = [];
                    foreach ($question->options as $option) {
                        $options[] = [
                            'id' => $option->id,
                            'option_label' => $option->option_label,
                            'option_text' => $option->option_text,
                        ];
                    }

                    $questions[] = [
                        'id' => $question->id,
                        'question_text' => $question->question_text,
                        'image_url' => $question->image_url,
                        'difficulty_level' => $question->difficulty_level,
                        'selected_option_id' => $saveAnswer[$question->id] ?? null,
                        'options' => $options,
                    ];
                }

                $sections[] = [
                    'id' => $section->id,
                    'title' => $section->title,
                    'duration_minutes' => $section->duration_minutes,
                    'order_index' => $section->order_index,
                    'questions' => $questions,
                ];
            }
        }

        return [
            'attempt_id' => $this->id,
            'status' => $this->status,
            'started_at' => $this->started_at?->toIsoString(),
            'total_duration_minutes' => $template?->total_duration_minutes,
            'session' => [
                'id' => $session?->id,
                'title' => $session?->title,
            ],
            'sections' => $sections,
        ];
    }
}
