<?php

namespace Database\Factories\Assessment\Exam;

use App\Models\Academic\Classroom;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\Assessment\Exam\ExamTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamSession>
 */
class ExamSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-2 days', '+2 days');
        $endTime = (clone $startTime)->modify('+6 hours');

        return [
            'exam_template_id' => ExamTemplate::factory(),
            'classroom_id' => fake()->boolean(60) ? Classroom::factory() : null,
            'title' => 'Sesi Ujian '.fake()->randomElement(['Pagi', 'Siang', 'Sore', 'Nasional']).' '.fake()->monthName(),
            'token' => strtoupper(fake()->bothify('??####')),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
