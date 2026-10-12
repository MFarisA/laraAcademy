<?php

namespace App\Actions\Assessment\Cbt;

use App\Enum\Academic\EnrollmentStatusEnum;
use App\Enum\Assessment\ExamAttemptStatusEnum;
use App\Models\Academic\Enrollment\ClassroomEnrollment;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StartExamAttemptAction
{
    /**
     * Memulai attempt baru atau me-resume attempt yang sedang berjalan.
     *
     * @throws ValidationException
     */
    public function handle(User $student, ExamSession $session): ExamAttempt
    {
        $now = now();

        // 1. Cek Jendela Waktu Sesi (Berdasarkan jam server)
        if ($session->start_time && $now->lt($session->start_time)) {
            throw ValidationException::withMessages([
                'session' => 'Sesi ujian ini belum dimulai.',
            ]);
        }

        if ($session->end_time && $now->gt($session->end_time)) {
            throw ValidationException::withMessages([
                'session' => 'Sesi ujian ini sudah berakhir.',
            ]);
        }

        // 2. Cek Hak Akses Kelas (Jika sesi terikat ke kelas tertentu)
        if ($session->classroom_id !== null) {
            $isEnrolled = ClassroomEnrollment::query()
                ->where('classroom_id', $session->classroom_id)
                ->where('student_id', $student->id)
                ->where('status', EnrollmentStatusEnum::ACTIVE)
                ->exists();

            if (! $isEnrolled) {
                throw ValidationException::withMessages([
                    'session' => 'Anda tidak terdaftar sebagai siswa aktif di kelas ujian ini.',
                ]);
            }
        }

        // 3. Database Transaction + Lock untuk Start / Resume Attempt
        return DB::transaction(function () use ($student, $session, $now) {
            $existingAttempt = ExamAttempt::query()
                ->where('exam_session_id', $session->id)
                ->where('student_id', $student->id)
                ->lockForUpdate()
                ->first();

            if ($existingAttempt) {
                if ($existingAttempt->status === ExamAttemptStatusEnum::IN_PROGRESS) {
                    return $existingAttempt;
                }

                throw ValidationException::withMessages([
                    'session' => 'Anda sudah tidak dapat mengakses sesi ujian ini.',
                ]);
            }

            return ExamAttempt::create([
                'exam_session_id' => $session->id,
                'student_id' => $student->id,
                'status' => ExamAttemptStatusEnum::IN_PROGRESS,
                'started_at' => $now,
            ]);
        });
    }
}
