<?php

namespace App\Http\Controllers\Assessment;

use App\Actions\Assessment\Exam\SaveExamSessionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\Exam\SaveExamSessionRequest;
use App\Http\Resources\Assessment\ExamSessionResource;
use App\Models\Academic\Classroom;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\Assessment\Exam\ExamTemplate;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExamSessionController extends Controller
{
    public function index(): Response
    {
        $examSession = ExamSession::query()
            ->with(['template', 'classroom'])
            ->withCount('attempts')
            ->latest('id')
            ->paginate(15);

        return Inertia::render('Assessment/ExamSession/Index', [
            'sessions' => ExamSessionResource::collection($examSession),
            'templates' => ExamTemplate::select(['id', 'title'])->orderBy('title')->get(),
            'classrooms' => Classroom::select(['id', 'name'])->orderBy('name')->get(),
        ]);
    }

    public function store(
        SaveExamSessionRequest $request,
        SaveExamSessionAction $action
    ): RedirectResponse {
        $action->handle($request->validated());
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Sesi ujian berhasil dibuat.',
        ]);

        return to_route('exam-sessions.index');
    }

    public function create(): Response
    {
        return Inertia::render('Assessment/ExamSession/Create', [
            'session' => ExamSessionResource::make(new ExamSession),
            'templates' => ExamTemplate::select(['id', 'title'])->orderBy('title')->get(),
            'classrooms' => Classroom::select(['id', 'name'])->orderBy('name')->get(),
        ]);
    }

    public function show(ExamSession $examSession): Response
    {
        return Inertia::render('Assessment/ExamSession/Show', [
            'session' => ExamSessionResource::make(
                $examSession->load(['template.sections', 'classroom'])->loadCount('attempts')
            ),
        ]);
    }

    public function update(
        SaveExamSessionRequest $request,
        ExamSession $examSession,
        SaveExamSessionAction $action
    ): RedirectResponse {
        $action->handle($request->validated(), $examSession);
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Sesi ujian berhasil diperbarui.',
        ]);

        return to_route('exam-sessions.index');
    }

    public function edit(ExamSession $examSession): Response
    {
        return Inertia::render('Assessment/ExamSession/Edit', [
            'session' => ExamSessionResource::make($examSession->load(['template', 'classroom'])),
            'templates' => ExamTemplate::select(['id', 'title'])->orderBy('title')->get(),
            'classrooms' => Classroom::select(['id', 'name'])->orderBy('name')->get(),
        ]);
    }

    public function destroy(ExamSession $examSession): RedirectResponse
    {
        $examSession->delete();
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Sesi ujian berhasil dihapus.',
        ]);

        return to_route('exam-sessions.index');
    }

    public function regenerateToken(
        ExamSession $examSession,
        SaveExamSessionAction $action
    ): RedirectResponse {
        $action->regenerateToken($examSession);
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Token sesi berhasil diperbarui.',
        ]);

        return back();
    }
}
