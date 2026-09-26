<?php

namespace App\Http\Controllers\Academic;

use App\Actions\Assessment\Question\StoreQuestionsAction;
use App\Actions\Assessment\Question\UpdateQuestionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Question\StoreQuestionRequest;
use App\Http\Requests\Academic\Question\UpdateQuestionRequest;
use App\Http\Resources\Academic\Question\QuestionResource;
use App\Models\Assessment\Question\Question;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function index(): Response
    {
        $questions = Question::query()
            ->with(['subject', 'options'])
            ->paginate(15);

        return Inertia::render('Academics/Assessment/Index', [
            'questions' => QuestionResource::collection($questions),
        ]);
    }

    public function store(
        StoreQuestionRequest $request,
        StoreQuestionsAction $action
    ): RedirectResponse {
        $action->handle($request->validated());

        return back()->with('success', 'Question created successfully');
    }

    public function show(Question $question): Response
    {
        return Inertia::render('Academic/Assessment/Show', [
            'questions' => new QuestionResource($question),
        ]);
    }

    public function update(
        UpdateQuestionRequest $request,
        UpdateQuestionAction $action,
        Question $questions
    ): RedirectResponse {
        $action->handle($questions, $request->validated());

        return back()->with('success', 'Question updated successfully');
    }

    public function destroy(Question $question): RedirectResponse
    {
        (bool) $question->delete();
        return back()->with('success', 'Question updated successfully');
    }
}
