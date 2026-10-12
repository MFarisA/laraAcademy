<?php

namespace App\Http\Controllers\Assessment;

use App\Actions\Assessment\Cbt\SaveExamAnswerAction;
use App\Actions\Assessment\Cbt\StartExamAttemptAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\Cbt\EnterCbtSessionRequest;
use App\Http\Requests\Assessment\Cbt\SaveCbtAnswerRequest;
use App\Http\Resources\Assessment\CbtExamSheetResource;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CbtController extends Controller
{
    public function enter(EnterCbtSessionRequest $request, ExamSession $examSession): JsonResponse|RedirectResponse
    {
        $inputToken = Str::upper($request->validated('token'));

        if ($examSession->token !== $inputToken) {
            throw ValidationException::withMessages([
                'token' => 'Token sesi ujian tidak valid.',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Token valid. Anda siap memulai ujian.',
                'session_id' => $examSession->id,
            ]);
        }

        return to_route('cbt.sessions.start', $examSession);
    }

    public function start(
        Request $request,
        ExamSession $examSession,
        StartExamAttemptAction $action,
    ): Response|JsonResponse {
        /** @var User $user */
        $user = $request->user();
        $attempt = $action->handle($user, $examSession);

        $attempt->loadMissing([
            'session.template.sections.questions.options',
            'answers',
        ]);

        if ($request->wantsJson()) {
            return (new CbtExamSheetResource($attempt))->response()->setStatusCode(200);
        }

        return Inertia::render('Assessment/Cbt/ExamSheet', [
            'attempt' => new CbtExamSheetResource($attempt),
        ]);
    }

    public function saveAnswer(
        SaveCbtAnswerRequest $request,
        ExamAttempt $attempt,
        SaveExamAnswerAction $action,
    ): JsonResponse {
        abort_unless($request->user()->id === $attempt->student_id, 403);
        $validated = $request->validated();
        $answer = $action->handle(
            attempt: $attempt,
            questionId: (int) $validated['question_id'],
            selectedOptionId: isset($validated['selected_option_id']) ? (int) $validated['selected_option_id'] : null,
            timeSpentSeconds: (int) ($validated['time_spent_seconds'] ?? 0),
        );

        return response()->json([
            'message' => 'Answer saved successfully',
            'answer' => $answer,
        ]);
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {
        // TODO: Kunci attempt ujian dan jalankan kalkulasi nilai akhir (scoring)
    }
}
