<?php

namespace App\Http\Controllers\Academic;

use App\Actions\Academic\Program\CreateProgramAction;
use App\Actions\Academic\Program\DeleteProgramAction;
use App\Actions\Academic\Program\UpdateProgramAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Program\StoreProgramRequest;
use App\Http\Requests\Academic\Program\UpdateProgramRequest;
use App\Http\Resources\Academic\Program\ProgramResource;
use App\Models\Academic\Program;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $programs = Program::query()->paginate(10);

        return Inertia::render('Academic/Program/Index', [
            'programs' => ProgramResource::collection($programs),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProgramRequest $request): RedirectResponse
    {
        Program::create($request->validated());

        return back()->with('success', 'Program created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program): Response
    {
        return Inertia::render('Academic/Program/Show', [
            'program' => new ProgramResource($program),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update($request->validated());

        return back()->with('success', 'Program updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return back()->with('success', 'Program deleted successfully');
    }
}
