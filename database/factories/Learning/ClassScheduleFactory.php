<?php

namespace Database\Factories\Learning;

use App\Models\Academic\Classroom;
use App\Models\Academic\Subject;
use App\Models\Learning\ClassSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassSchedule>
 */
class ClassScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomType = fake()->randomElement(['offline', 'online']);

        return [
            'classroom_id' => Classroom::factory(),
            'subject_id' => Subject::factory(),
            'instructor_id' => User::factory(),
            'room_type' => $roomType,
            'meeting_link' => $roomType === 'online' ? fake()->url() : null,
            'scheduled_at' => fake()->dateTimeBetween('-1 week', '+2 weeks'),
            'duration_minutes' => fake()->randomElement([60, 90, 120]),
        ];
    }
}
