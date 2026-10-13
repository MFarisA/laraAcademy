<?php

namespace Database\Factories\Academic;

use App\Models\Academic\Program;
use App\Models\Academic\ProgramSubject;
use App\Models\Academic\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramSubject>
 */
class ProgramSubjectFactory extends Factory
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
            'subject_id' => Subject::factory(),
            'min_passing_score' => fake()->randomElement([65.00, 70.00, 80.00, 156.00]),
        ];
    }
}
