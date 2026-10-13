<?php

namespace Database\Factories\Assessment\Exam;

use App\Models\Academic\Program;
use App\Models\Assessment\Exam\ExamTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamTemplate>
 */
class ExamTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'title' => 'Simulasi CAT '.fake()->randomElement(['SKD CPNS', 'Kedinasan Nasional', 'SNBT TPS']).' #'.fake()->numberBetween(1, 20),
            'type' => fake()->randomElement(['skd', 'snbt', 'kedinasan', 'simulation']),
            'total_duration_minutes' => fake()->randomElement([100, 110, 120]),
        ];
    }
}
