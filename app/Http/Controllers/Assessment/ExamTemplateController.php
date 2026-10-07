<?php

namespace App\Http\Controllers\Assessment;

use App\Actions\Assessment\Exam\SaveExamTemplateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\Exam\SaveExamTemplateRequest;
use App\Http\Resources\Assessment\ExamTemplateResource;
use App\Models\Assessment\Exam\ExamTemplate;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExamTemplateController extends Controller
{
    public function index(): Response
    {
        $templates = ExamTemplate::query()
            ->with(['program', 'sections.subject'])
            ->latest('id')
            ->paginate(15);

        return Inertia::render('Assessment/ExamTemplate/Index', [
            'templates' => ExamTemplateResource::collection($templates),
        ]);
    }

    public function store(
        SaveExamTemplateRequest $request,
        SaveExamTemplateAction $action
    ): RedirectResponse {
        $action->handle($request->validated());
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Template ujian berhasil dibuat.',
        ]);

        return back();
    }

    public function show(ExamTemplate $examTemplate): Response
    {
        return Inertia::render('Assessment/ExamTemplate/Show', [
            'template' => $examTemplate->load(['program', 'sections.subject']),
        ]);
    }

    public function update(
        SaveExamTemplateRequest $request,
        SaveExamTemplateAction $action,
        ExamTemplate $examTemplate
    ): RedirectResponse {
        $action->handle($request->validated(), $examTemplate);
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Template ujian berhasil dibuat.',
        ]);

        return back();
    }

    public function destroy(ExamTemplate $examTemplate): RedirectResponse
    {
        $examTemplate->delete();
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Template ujian berhasil dibuat.',
        ]);

        return back();
    }
}
