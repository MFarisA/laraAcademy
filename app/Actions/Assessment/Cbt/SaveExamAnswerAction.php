<?php

namespace App\Actions\Assessment\Cbt;

use App\Models\Assessment\Exam\ExamAnswer;
use App\Models\Assessment\Exam\ExamAttempt;

class SaveExamAnswerAction
{
    public function handle(
        ExamAttempt $attempt,
        int $questionId,
        ?int $selectedOptionId = null,
        int $timeSpentSeconds = 0
    ): ExamAnswer {
        // TODO: Validasi status attempt & deadline server, lalu simpan jawaban ke exam_answers
        return new ExamAnswer;
    }
}
