<?php

namespace Database\Seeders;

use App\Enum\Learning\AttendanceStatusEnum;
use App\Models\Learning\Attendance;
use App\Models\Learning\ClassSchedule;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pastSchedules = ClassSchedule::where('scheduled_at', '<', now())->with('classroom.students')->get();

        foreach ($pastSchedules as $schedule) {
            $students = $schedule->classroom?->students ?? [];

            foreach ($students as $student) {
                Attendance::firstOrCreate(
                    [
                        'class_schedule_id' => $schedule->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => fake()->randomElement([
                            AttendanceStatusEnum::PRESENT->value,
                            AttendanceStatusEnum::PRESENT->value,
                            AttendanceStatusEnum::PERMITTED->value,
                            AttendanceStatusEnum::SICK->value,
                            AttendanceStatusEnum::ABSENT->value,
                        ]),
                        'verified_at' => (clone $schedule->scheduled_at)->modify('+2 hours'),
                    ]
                );
            }
        }
    }
}
