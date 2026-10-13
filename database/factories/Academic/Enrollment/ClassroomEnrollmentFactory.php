<?php

namespace Database\Factories\Academic\Enrollment;

use App\Models\Academic\Classroom;
use App\Models\Academic\Enrollment\ClassroomEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassroomEnrollment>
 */
class ClassroomEnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classroom_id' => Classroom::factory(),
            'student_id' => User::factory(),
            'status' => fake()->randomElement(['active', 'active', 'active', 'finished']),
            'enrolled_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
