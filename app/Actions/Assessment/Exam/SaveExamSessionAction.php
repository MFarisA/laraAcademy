<?php

namespace App\Actions\Assessment\Exam;

use App\Models\Assessment\Exam\ExamSession;
use Illuminate\Support\Str;

class SaveExamSessionAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?ExamSession $session = null): ExamSession
    {
        $session = $session ?? new ExamSession();
        $data['token'] = $data['token'] ?? ExamSession::generateUniqueToken();
        $session->fill($data)->save();
        return $session;
    }

    public function regenerateToken(ExamSession $session): ExamSession
    {
        $session->update([
            'token' => ExamSession::generateUniqueToken(),
        ]);
        return $session;
    }
}
