<?php

namespace App\Actions\Action\Academic\Enrollment;

use App\Enum\Academic\EnrollmentStatusEnum;
use App\Models\Academic\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class EnrollmentStudentAction
{
    use AsAction;

    public function handle(Classroom $classroom, User $student): Classroom
    {
        return DB::transaction(function () use ($classroom, $student) {
            // 1. Kunci baris Classroom dengan lockForUpdate untuk mencegah race condition kuota
            /** @var Classroom $lockedClassroom */
            $lockedClassroom = Classroom::query()
                ->where('id', $classroom->id)
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Cek apakah siswa sudah terdaftar di kelas ini
            $alreadyEnrolled = $lockedClassroom->students()
                ->where('student_id', $student->id)
                ->exists();

            if ($alreadyEnrolled) {
                throw ValidationException::withMessages([
                    'student_id' => 'Siswa sudah terdaftar dikelas ini.',
                ]);
            }

            // 3. Hitung jumlah siswa aktif saat ini
            $activeStudentCount = $lockedClassroom->students()
                ->wherePivot('status', EnrollmentStatusEnum::ACTIVE->value)
                ->count();

            // 4. Validasi kapasitas kelas
            if ($activeStudentCount >= $lockedClassroom->capacity) {
                throw ValidationException::withMessages([
                    'classroom_id' => 'Kapasitas kelas sudah penuh (maksimal' . $lockedClassroom->capacity . 'siswa).',
                ]);
            }

            // 5. Masukkan siswa ke pivot classroom_enrollments
            $lockedClassroom->students()->attach($student->id, [
                'status' => EnrollmentStatusEnum::ACTIVE->value,
                'enrolled_at' => now(),
            ]);
            return $lockedClassroom->load('students');
        });
    }
}
