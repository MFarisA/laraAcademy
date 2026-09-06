<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Subject\StoreSubjectRequest;
use App\Http\Requests\Academic\Subject\UpdateSubjectRequest;
use App\Http\Resources\Academic\SubjectResource;
use App\Models\Academic\Subject;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    public function index(): Response
    {
        $subjects = Subject::query()
            ->latest()
            ->get();

        return Inertia::render('Subjects/Index', [
            'subjects' => SubjectResource::collection($subjects),
        ]);
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return to_route('subjects.index')->with('success', 'subjects successfully created.');
    }

    public function show(Subject $subject): Response
    {
        return Inertia::render('Subjects/Show', [
            'subject' => new SubjectResource($subject),
        ]);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return to_route('subjects.index')->with('success', 'subjects successfully updated.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return to_route('subjects.index')->with('success', 'subjects successfully updated.');
    }
}
