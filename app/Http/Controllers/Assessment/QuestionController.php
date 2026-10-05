<?php

namespace App\Http\Controllers\Assessment;

use App\Actions\Assessment\Question\StoreQuestionsAction;
use App\Actions\Assessment\Question\UpdateQuestionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Assessment\Question\FilterQuestionRequest;
use App\Http\Requests\Assessment\Question\StoreQuestionRequest;
use App\Http\Requests\Assessment\Question\UpdateQuestionRequest;
use App\Http\Resources\Academic\Subject\SubjectResource;
use App\Http\Resources\Assessment\Question\QuestionResource;
use App\Models\Academic\Subject;
use App\Models\Assessment\Question\Question;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function index(FilterQuestionRequest $request): Response
    {
        $validated = $request->validated();
        $questions = Question::query()
            ->with(['subject', 'options'])
            ->filter($validated)
            ->paginate($perPage = $request->integer('per_page', 15))
            ->withQueryString();

        $subjects = Subject::query()
            ->select(['id', 'name', 'code'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Academic/Assessment/Index', [
            'questions' => QuestionResource::collection($questions),
            'subjects' => SubjectResource::collection($subjects),
            'filters' => [
                'search' => $validated['search'] ?? null,
                'subject_id' => $validated['subject_id'] ?? null,
                'difficulty_level' => $validated['difficulty_level'] ?? null,
                'grading_rule' => $validated['grading_rule'] ?? null,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function store(
        StoreQuestionRequest $request,
        StoreQuestionsAction $action
    ): RedirectResponse {
        $action->handle($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Question created successfully']);

        return back();
    }

    public function create(): Response
    {
        return Inertia::render('Academic/Assessment/Create', [
            'subjects' => SubjectResource::collection(
                Subject::query()->select(['id', 'name', 'code'])->orderBy('name')->get()
            ),
        ]);
    }

    public function show(Question $question): Response
    {
        return Inertia::render('Academic/Assessment/Show', [
            'question' => new QuestionResource($question->load(['subject', 'options'])),
        ]);
    }

    public function update(
        UpdateQuestionRequest $request,
        UpdateQuestionAction $action,
        Question $question
    ): RedirectResponse {
        $action->handle($question, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Question updated successfully']);

        return back();
    }

    public function edit(Question $question): Response
    {
        return Inertia::render('Academic/Assessment/Edit', [
            'question' => new QuestionResource($question->load(['subject', 'options'])),
            'subjects' => SubjectResource::collection(
                Subject::query()->select(['id', 'name', 'code'])->orderBy('name')->get()
            ),
        ]);
    }

    public function destroy(Question $question): RedirectResponse
    {
        (bool) $question->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Question deleted successfully']);

        return back();
    }
}
