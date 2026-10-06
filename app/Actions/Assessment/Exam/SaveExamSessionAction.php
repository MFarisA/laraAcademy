<?php

namespace App\Actions\Assessment\Exam;

use App\Models\Assessment\Exam\ExamSession;

class SaveExamSessionAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?ExamSession $session = null): ExamSession
    {
        // TODO: Simpan sesi ujian baru/update dan pastikan memiliki token unik
        return $session ?? new ExamSession;
    }

    public function regenerateToken(ExamSession $session): ExamSession
    {
        // TODO: Buat token acak unik baru dan update ke sesi
        return $session;
    }
}
