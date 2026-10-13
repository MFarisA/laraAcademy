<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Classroom\StoreClassroomRequest;
use App\Http\Requests\Academic\Classroom\UpdateClassroomRequest;
use App\Http\Resources\Academic\Classroom\ClassRoomResource;
use App\Models\Academic\Batch;
use App\Models\Academic\Classroom;
use App\Models\Organization\Branch;
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
            'classrooms' => ClassRoomResource::collection($classRoom),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClassroomRequest $request): RedirectResponse
    {
        Classroom::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Classroom created successfully']);

        return to_route('classrooms.index');
    }

    public function create(): Response
    {
        return Inertia::render('Academic/Classroom/Create', [
            'classroom' => ClassRoomResource::make(new Classroom),
            'batches' => Batch::select('id', 'name')->orderBy('name')->get(),
            'branches' => Branch::select('id', 'name')->orderBy('name')->get(),
        ]);
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

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Classroom updated successfully']);

        return to_route('classrooms.index');
    }

    public function edit(Classroom $classroom): Response
    {
        return Inertia::render('Academic/Classroom/Edit', [
            'classroom' => ClassRoomResource::make($classroom),
            'batches' => Batch::select('id', 'name')->orderBy('name')->get(),
            'branches' => Branch::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom): RedirectResponse
    {
        $classroom->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Classroom deleted successfully']);

        return to_route('classrooms.index');
    }
}
