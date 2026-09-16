<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Batch\StoreBatchRequest;
use App\Http\Requests\Academic\Batch\UpdateBatchRequest;
use App\Http\Resources\Academic\BatchResource;
use App\Models\Academic\Batch;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BatchController extends Controller
{
    public function index(): Response
    {
        $batch = Batch::query()
            ->with('program')
            ->withCount('classrooms')
            ->latest()
            ->paginate(15);

        return Inertia::render('Batches/Index', [
            'Batches' => BatchResource::collection($batch),
        ]);
    }

    public function store(StoreBatchRequest $request): RedirectResponse
    {
        Batch::create($request->validated());
        return to_route('batches.index')->with('success', 'batches successfully created.');
    }

    public function show(Batch $batch): Response
    {
        return Inertia::render('Batches/Show', [
            'batch' => BatchResource::make($batch->load(['program', 'classrooms'])),
        ]);
    }

    public function update(UpdateBatchRequest $request, Batch $batch): RedirectResponse
    {
        $batch->update($request->validated());
        return to_route('batches.index')->with('success', 'Batch successfully updated.');
    }

    public function destroy(Batch $batch): RedirectResponse
    {
        $batch->delete();
        return to_route('batches.index')->with('success', 'batches successfully deleted.');
    }
}
