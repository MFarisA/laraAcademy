<?php

namespace Database\Seeders;

use App\Enum\Access\RoleRegistryEnum;
use App\Models\Academic\Classroom;
use App\Models\Academic\Enrollment\ClassroomEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassroomEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = Classroom::with('branch')->get();

        foreach ($classrooms as $classroom) {
            // Ambil siswa yang satu cabang dengan kelas ini
            $students = User::role(RoleRegistryEnum::STUDENT->value)
                ->where('branch_id', $classroom->branch_id)
                ->take(15)
                ->get();

            foreach ($students as $student) {
                ClassroomEnrollment::firstOrCreate(
                    [
                        'classroom_id' => $classroom->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => 'active',
                        'enrolled_at' => now()->subMonth(),
                    ]
                );
            }
        }
    }
}
