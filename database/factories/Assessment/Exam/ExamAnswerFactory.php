<?php

namespace Database\Factories\Assessment\Exam;

use App\Models\Assessment\Exam\ExamAnswer;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamAnswer>
 */
class ExamAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_attempt_id' => ExamAttempt::factory(),
            'question_id' => Question::factory(),
            'selected_option_id' => fn (array $attrs) => QuestionOption::where('question_id', $attrs['question_id'])->inRandomOrder()->value('id'),
            'time_spent_seconds' => fake()->numberBetween(10, 120),
        ];
    }
}
