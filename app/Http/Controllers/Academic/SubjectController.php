<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Subject\StoreSubjectRequest;
use App\Http\Requests\Academic\Subject\UpdateSubjectRequest;
use App\Http\Resources\Academic\Subject\SubjectResource;
use App\Models\Academic\Subject;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $subjects = Subject::query()->paginate(10);

        return Inertia::render('Academic/Subject/Index', [
            'subjects' => SubjectResource::collection($subjects),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return back()->with('success', 'Subject created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject): Response
    {
        return Inertia::render('Academic/Subject/Show', [
            'subject' => new SubjectResource($subject),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return back()->with('success', 'Subject updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return back()->with('success', 'Subject deleted successfully');
    }
}
