<?php

namespace App\Actions\Assessment\Cbt;

use App\Enum\Assessment\ExamAttemptStatusEnum;
use App\Models\Assessment\Exam\ExamAnswer;
use App\Models\Assessment\Exam\ExamAttempt;
use Illuminate\Validation\ValidationException;

class SaveExamAnswerAction
{
    public function handle(
        ExamAttempt $attempt,
        int $questionId,
        ?int $selectedOptionId = null,
        int $timeSpentSeconds = 0
    ): ExamAnswer {
        if ($attempt->status !== ExamAttemptStatusEnum::IN_PROGRESS) {
            throw ValidationException::withMessages([
                'attempt' => 'Ujian ini sudah selesai atau di tutup',
            ]);
        }
        $template = $attempt->session?->template;
        if ($template && $template->total_duration_minutes > 0) {
            $deadline = $attempt->started_at?->copy()
                ->addMinutes($template->total_duration_minutes)
                ->addSeconds(60);
            if ($deadline && now()->gt($deadline)) {
                $attempt->update([
                    'status' => ExamAttemptStatusEnum::EXPIRED,
                ]);
                throw ValidationException::withMessages([
                    'attempt' => 'Waktu Pengerjaan ujian Anda telah habis.',
                ]);
            }
        }

        return ExamAnswer::query()->updateOrCreate(
            [
                'exam_attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'selected_option_id' => $selectedOptionId,
                'time_spent_seconds' => $timeSpentSeconds,
            ],
        );
    }
}
