<?php

namespace Database\Factories\Assessment\Exam;

use App\Models\Assessment\Exam\ExamSection;
use App\Models\Assessment\Exam\ExamSectionQuestion;
use App\Models\Assessment\Question\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamSectionQuestion>
 */
class ExamSectionQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_section_id' => ExamSection::factory(),
            'question_id' => Question::factory(),
            'order_index' => fake()->numberBetween(1, 50),
        ];
    }
}
