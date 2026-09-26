<?php

namespace App\Actions\Assessment\Question;

use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Assessment\Question\Question;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateQuestionAction
{
    use AsAction;

    /**
     * @param  array<mixed>  $data
     */
    public function handle(Question $question, array $data): Question
    {
        return DB::transaction(function () use ($question, $data) {
            $rawRule = $data['grading_rule'] ?? $question->grading_rule;
            $gradingRule = $rawRule instanceof GradingRuleEnum
                ? $rawRule
                : GradingRuleEnum::from((string) $rawRule);

            $question->update([
                'subject_id' => $data['subject_id'] ?? $question->subject_id,
                'question_text' => $data['question_text'] ?? $question->question_text,
                'image_url' => \array_key_exists('image_url', $data) ? $data['image_url'] : $question->image_url,
                'grading_rule' => $gradingRule,
                'difficulty_level' => $data['difficulty_level'] ?? $question->difficulty_level,
            ]);

            if (isset($data['options']) && \is_array($data['options'])) {
                $question->options()->delete();
                $options = Arr::map(
                    $data['options'],
                    fn (array $opt) => $this->transformOption($opt, $gradingRule)
                );
                $question->options()->createMany($options);
            }

            return $question->fresh(['subjects', 'options']) ?? $question;
        });
    }

    /**
     * @param  array<mixed>  $option
     * @return array{option_label: mixed, option_text: mixed, is_correct: bool, weight_score: float|0}
     */
    public function transformOption(array $option, GradingRuleEnum $rule): array
    {
        $isTkp = $rule === GradingRuleEnum::TKP;
        $isCorrect = ! $isTkp && ! empty($option['is_correct']);

        return [
            'option_label' => $option['option_label'],
            'option_text' => $option['option_text'],
            'is_correct' => $isCorrect,
            'weight_score' => $isTkp ? (float) ($option['weight_score'] ?? 0) : ($isCorrect ? (float) ($option['weight_score'] ?? 1) : 0),
        ];
    }
}
