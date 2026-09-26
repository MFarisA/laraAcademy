<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Classroom\StoreClassroomRequest;
use App\Http\Requests\Academic\Classroom\UpdateClassroomRequest;
use App\Http\Resources\Academic\Classroom\ClassRoomResource;
use App\Models\Academic\Classroom;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $classRoom = Classroom::query()->with(['batch', 'branch'])->paginate(10);

        return Inertia::render('Academic/Classroom/Index', [
            'Classrooms' => ClassRoomResource::collection($classRoom),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassroomRequest $request): RedirectResponse
    {
        Classroom::create($request->validated());

        return back()->with('success', 'Classroom created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom): Response
    {
        return Inertia::render('Academic/Classroom/Show', [
            'classroom' => ClassRoomResource::make($classroom->load(['batch', 'branch'])),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        $classroom->update($request->validated());

        return back()->with('success', 'Classroom updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom): RedirectResponse
    {
        $classroom->delete();

        return back()->with('success', 'Classroom deleted successfully');
    }
}
