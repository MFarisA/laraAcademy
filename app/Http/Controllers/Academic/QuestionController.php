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

        return Inertia::render('Academic/Assessment/Index', [
            'questions' => QuestionResource::collection($questions),
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

    public function show(Question $question): Response
    {
        return Inertia::render('Academic/Assessment/Show', [
            'question' => new QuestionResource($question),
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

    public function destroy(Question $question): RedirectResponse
    {
        (bool) $question->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Question deleted successfully']);

        return back();
    }
}
