<?php

namespace App\Actions\Assessment\Cbt;

use App\Models\Assessment\Exam\ExamAttempt;

class ScoreAttemptAction
{
    public function handle(ExamAttempt $attempt): ExamAttempt
    {
        // TODO: Hitung skor per subtes (rule Standard vs TKP), simpan ke exam_section_results, dan agregasi total_score ke attempt
        return $attempt;
    }
}
