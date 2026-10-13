<?php

namespace App\Http\Controllers\Assessment;

use App\Actions\Assessment\Exam\SaveExamTemplateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\Exam\SaveExamTemplateRequest;
use App\Http\Resources\Assessment\ExamTemplateResource;
use App\Models\Academic\Program;
use App\Models\Academic\Subject;
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

        return to_route('exam-templates.index');
    }

    public function create(): Response
    {
        return Inertia::render('Assessment/ExamTemplate/Create', [
            'template' => ExamTemplateResource::make(new ExamTemplate),
            'programs' => Program::select('id', 'name')->orderBy('name')->get(),
            'subjects' => Subject::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(ExamTemplate $examTemplate): Response
    {
        return Inertia::render('Assessment/ExamTemplate/Show', [
            'template' => ExamTemplateResource::make(
                $examTemplate->load(['program', 'sections.subject'])
            ),
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
            'message' => 'Template ujian berhasil diupdate.',
        ]);

        return to_route('exam-templates.index');
    }

    public function edit(ExamTemplate $examTemplate): Response
    {
        return Inertia::render('Assessment/ExamTemplate/Edit', [
            'template' => ExamTemplateResource::make(
                $examTemplate->load(['program', 'sections.subject'])
            ),
            'programs' => Program::select('id', 'name')->orderBy('name')->get(),
            'subjects' => Subject::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function destroy(ExamTemplate $examTemplate): RedirectResponse
    {
        $examTemplate->delete();
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Template ujian berhasil dihapus.',
        ]);

        return to_route('exam-templates.index');
    }
}
