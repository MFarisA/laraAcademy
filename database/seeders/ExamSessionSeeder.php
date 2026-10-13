<?php

namespace Database\Seeders;

use App\Enum\Access\RoleRegistryEnum;
use App\Enum\Assessment\ExamAttemptStatusEnum;
use App\Models\Academic\Classroom;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\Assessment\Exam\ExamTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skdTemplate = ExamTemplate::where('type', 'skd')->first() ?? ExamTemplate::first();
        $snbtTemplate = ExamTemplate::where('type', 'snbt')->first() ?? ExamTemplate::latest()->first();

        $firstClassroom = Classroom::first();

        // 1. Sesi Akbar Nasional (Publik - Terbuka untuk semua siswa)
        // Token: 'SKD2026'
        $publicSession = ExamSession::firstOrCreate(
            ['token' => 'SKD2026'],
            [
                'exam_template_id' => $skdTemplate->id,
                'classroom_id' => null, // publik
                'title' => 'Tryout Akbar SKD CPNS & Kedinasan Nasional 2026',
                'start_time' => now()->subDay(),
                'end_time' => now()->addDays(3),
            ]
        );

        // 2. Sesi Khusus Kelas
        // Token: 'KLAS01'
        if ($firstClassroom) {
            ExamSession::firstOrCreate(
                ['token' => 'KLAS01'],
                [
                    'exam_template_id' => $skdTemplate->id,
                    'classroom_id' => $firstClassroom->id,
                    'title' => "Simulasi Mandiri {$firstClassroom->name}",
                    'start_time' => now()->subHours(6),
                    'end_time' => now()->addDays(2),
                ]
            );
        }

        // 3. Sesi Mendatang
        // Token: 'SNBT26'
        if ($snbtTemplate) {
            ExamSession::firstOrCreate(
                ['token' => 'SNBT26'],
                [
                    'exam_template_id' => $snbtTemplate->id,
                    'classroom_id' => null,
                    'title' => 'Simulasi UTBK SNBT Batch 1 (Akan Datang)',
                    'start_time' => now()->addDays(2),
                    'end_time' => now()->addDays(5),
                ]
            );
        }

        // 4. Sample pengerjaan siswa pada sesi publik
        $students = User::role(RoleRegistryEnum::STUDENT->value)->take(10)->get();

        foreach ($students as $index => $student) {
            if ($student->email === 'siswa@x.com') {
                // Untuk user login demo 'siswa@x.com', status in_progress agar bisa dicoba "Lanjutkan Ujian"
                ExamAttempt::firstOrCreate(
                    [
                        'exam_session_id' => $publicSession->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'started_at' => now()->subMinutes(20),
                        'submitted_at' => null,
                        'total_score' => 0.00,
                        'is_passed' => false,
                        'status' => ExamAttemptStatusEnum::IN_PROGRESS,
                    ]
                );
            } else {
                // Siswa lainnya sudah completed dengan skor realistis
                ExamAttempt::firstOrCreate(
                    [
                        'exam_session_id' => $publicSession->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'started_at' => now()->subHours(2),
                        'submitted_at' => now()->subMinutes(30),
                        'total_score' => fake()->randomFloat(2, 310, 485),
                        'is_passed' => $index % 4 !== 0,
                        'status' => ExamAttemptStatusEnum::COMPLETED,
                    ]
                );
            }
        }
    }
}
