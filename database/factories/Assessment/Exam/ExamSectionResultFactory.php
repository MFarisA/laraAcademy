<?php

namespace Database\Factories\Assessment\Exam;

use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSection;
use App\Models\Assessment\Exam\ExamSectionResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamSectionResult>
 */
class ExamSectionResultFactory extends Factory
{
    protected $model = ExamSectionResult::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_attempt_id' => ExamAttempt::factory(),
            'exam_section_id' => ExamSection::factory(),
            'score' => fake()->randomFloat(2, 50, 150),
            'is_passed' => fake()->boolean(70),
        ];
    }
}
