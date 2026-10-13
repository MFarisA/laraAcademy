<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            BranchSeeder::class,
            SubjectSeeder::class,
            ProgramSeeder::class,
            BatchSeeder::class,
            ClassroomSeeder::class,
            ClassroomEnrollmentSeeder::class,
            ClassScheduleSeeder::class,
            LearningMaterialSeeder::class,
            AttendanceSeeder::class,
            QuestionSeeder::class,
            ExamTemplateSeeder::class,
            ExamSessionSeeder::class,
        ]);
    }
}
