<?php

namespace App\Actions\Assessment\Cbt;

use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\User;

class StartExamAttemptAction
{
    public function handle(User $student, ExamSession $session): ExamAttempt
    {
        // TODO: Validasi jendela waktu sesi, hak akses kelas, dan buat/resume attempt ujian
        return new ExamAttempt;
    }
}
