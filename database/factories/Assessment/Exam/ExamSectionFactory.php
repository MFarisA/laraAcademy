<?php

namespace Database\Factories\Assessment\Exam;

use App\Models\Academic\Subject;
use App\Models\Assessment\Exam\ExamSection;
use App\Models\Assessment\Exam\ExamTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamSection>
 */
class ExamSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_template_id' => ExamTemplate::factory(),
            'subject_id' => Subject::factory(),
            'title' => fake()->randomElement([
                'Tes Wawasan Kebangsaan (TWK)',
                'Tes Inteligensi Umum (TIU)',
                'Tes Karakteristik Pribadi (TKP)',
                'Penalaran Umum',
                'Pemahaman Bacaan dan Menulis',
            ]),
            'passing_grade' => fake()->randomElement([65.00, 80.00, 156.00, 166.00]),
            'duration_minutes' => fake()->randomElement([30, 35, 40]),
            'order_index' => fake()->numberBetween(1, 4),
        ];
    }
}
