<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Program\StoreProgramRequest;
use App\Http\Requests\Academic\Program\UpdateProgramRequest;
use App\Http\Resources\Academic\ProgramResource;
use App\Models\Academic\Program;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(): Response
    {
        $programs = Program::query()
            ->latest()
            ->get();

        return Inertia::render('Programs/Index', [
            'programs' => ProgramResource::collection($programs),
        ]);
    }

    public function store(StoreProgramRequest $request): RedirectResponse
    {
        Program::create($request->validated());

        return to_route('programs.index')->with('success', 'programs successfully created.');
    }

    public function show(Program $program): Response
    {
        return Inertia::render('Programs/Show', [
            'program' => new ProgramResource($program),
        ]);
    }

    public function update(UpdateProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update($request->validated());

        return to_route('programs.index')->with('success', 'programs successfully updated.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return to_route('programs.index')->with('success', 'programs successfully deleted.');
    }
}
