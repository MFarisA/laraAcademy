<?php

namespace App\Actions\Assessment\Cbt;

use App\Enum\Assessment\ExamAttemptStatusEnum;
use App\Models\Assessment\Exam\ExamAttempt;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CalculateSessionRanksAction
{
    use AsAction;

    public function handle(int $sessionId): void
    {
        DB::transaction(function () use ($sessionId) {
            $attempts = ExamAttempt::query()
                ->where('exam_session_id', $sessionId)
                ->where('status', ExamAttemptStatusEnum::COMPLETED)
                ->with('student:id,branch_id')
                ->get();
            if ($attempts->isEmpty()) {
                return;
            }

            $sorted = $attempts->sortBy([
                ['total_score', 'desc'],
                ['duration_seconds', 'asc'],
                ['id', 'asc'],
            ])->values();

            $branchCounters = [];
            foreach ($sorted as $index => $attempt) {
                $branchId = $attempt->student?->branch_id;
                $branchRank = null;

                if ($branchId !== null) {
                    $branchCounters[$branchId] = ($branchCounters[$branchId] ?? 0) + 1;
                    $branchRank = $branchCounters[$branchId];
                }

                $attempt->forceFill([
                    'national_rank' => $index + 1,
                    'branch_rank' => $branchRank,
                ])->saveQuietly();
            }
        });
    }
}
