<?php

namespace App\Actions\Assessment\Question;

use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Assessment\Question\Question;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreQuestionsAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): Question
    {
        return DB::transaction(function () use ($data) {
            $gradingRule = GradingRuleEnum::from($data['grading_rule']);
            $question = Question::create([
                'subject_id' => $data['subject_id'],
                'question_text' => $data['question_text'],
                'image_url' => $data['image_url'] ?? null,
                'grading_rule' => $gradingRule,
                'difficulty_level' => $data['difficulty_level'],
            ]);

            $options = Arr::map(
                $data['options'] ?? [],
                fn (array $opt) => $this->transformOption($opt, $gradingRule),
            );
            $question->options()->createMany($options);

            return $question->load(['subject', 'options']);
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
