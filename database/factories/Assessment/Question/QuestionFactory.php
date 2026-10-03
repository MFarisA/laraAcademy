<?php

namespace Database\Factories\Assessment\Question;

use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Academic\Subject;
use App\Models\Assessment\Question\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'question_text' => fake()->sentence(12),
            'grading_rule' => GradingRuleEnum::STANDARD,
            'difficulty_level' => 'medium',
        ];
    }

    public function tkp(): static
    {
        return $this->state(fn (): array => [
            'grading_rule' => GradingRuleEnum::TKP,
        ]);
    }

    public function difficulty(string $level): static
    {
        return $this->state(fn (): array => [
            'difficulty_level' => $level,
        ]);
    }
}
