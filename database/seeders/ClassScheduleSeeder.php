<?php

namespace Database\Seeders;

use App\Enum\Access\RoleRegistryEnum;
use App\Models\Academic\Classroom;
use App\Models\Academic\Subject;
use App\Models\Learning\ClassSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = Classroom::take(6)->get();
        $subjects = Subject::all();

        foreach ($classrooms as $classroom) {
            $instructors = User::role(RoleRegistryEnum::INSTRUCTOR->value)
                ->where('branch_id', $classroom->branch_id)
                ->get();

            if ($instructors->isEmpty()) {
                $instructors = User::role(RoleRegistryEnum::INSTRUCTOR->value)->get();
            }

            $instructor = $instructors->first();

            if (! $instructor) {
                continue;
            }

            $times = [
                now()->subDays(3)->setTime(9, 0),
                now()->subDay()->setTime(13, 30),
                now()->addDays(2)->setTime(10, 0),
                now()->addDays(5)->setTime(15, 0),
            ];

            foreach ($times as $index => $time) {
                $subject = $subjects[$index % $subjects->count()];
                $isOnline = $index % 2 === 1;

                ClassSchedule::firstOrCreate(
                    [
                        'classroom_id' => $classroom->id,
                        'subject_id' => $subject->id,
                        'scheduled_at' => $time,
                    ],
                    [
                        'instructor_id' => $instructor->id,
                        'room_type' => $isOnline ? 'online' : 'offline',
                        'meeting_link' => $isOnline ? 'https://meet.google.com/xyz-'.fake()->lexify('???-???') : null,
                        'duration_minutes' => 90,
                    ]
                );
            }
        }
    }
}
