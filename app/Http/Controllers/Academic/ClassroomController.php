<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Classroom\StoreClassroomRequest;
use App\Http\Requests\Academic\Classroom\UpdateClassroomRequest;
use App\Http\Resources\Academic\ClassRoomResource;
use App\Models\Academic\Classroom;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;


class ClassroomController extends Controller
{
    public function index(): Response
    {
        $classRoom = Classroom::query()
            ->with(['branch', 'batch'])
            ->latest()
            ->withCount('students')
            ->paginate(15);

        return Inertia::render('Classrooms/Index', [
            'Classrooms' => ClassRoomResource::collection($classRoom),
        ]);
    }

    public function store(StoreClassroomRequest $request): RedirectResponse
    {
        Classroom::create($request->validated());
        return to_route('classrooms.index')->with('success', 'Classrooms successfully created.');
    }

    public function show(Classroom $classroom): Response
    {
        return Inertia::render('Classrooms/Show', [
            'classroom' => ClassroomResource::make($classroom->load(['batch', 'branch'])),
        ]);
    }

    public function update(UpdateClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        $classroom->update($request->validated());
        return to_route('classrooms.index')->with('success', 'Classrooms successfully created.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        $classroom->delete();
        return to_route('classrooms.index')->with('success', 'Classrooms successfully deleted.');
    }
}
