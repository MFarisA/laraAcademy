<?php

namespace Database\Factories\Learning;

use App\Enum\Learning\AttendanceStatusEnum;
use App\Models\Learning\Attendance;
use App\Models\Learning\ClassSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_schedule_id' => ClassSchedule::factory(),
            'student_id' => User::factory(),
            'status' => fake()->randomElement([
                AttendanceStatusEnum::PRESENT,
                AttendanceStatusEnum::PRESENT,
                AttendanceStatusEnum::PERMITTED,
                AttendanceStatusEnum::SICK,
                AttendanceStatusEnum::ABSENT,
            ]),
            'verified_at' => now(),
        ];
    }
}
