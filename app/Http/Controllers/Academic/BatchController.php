<?php

namespace App\Http\Controllers\Academic;

use App\Actions\Academic\Batch\CreateBatchAction;
use App\Actions\Academic\Batch\DeleteBatchAction;
use App\Actions\Academic\Batch\UpdateBatchAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Batch\StoreBatchRequest;
use App\Http\Requests\Academic\Batch\UpdateBatchRequest;
use App\Http\Resources\Academic\Batch\BatchResource;
use App\Models\Academic\Batch;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $batch = Batch::query()->with('program')->paginate(10);

        return Inertia::render('Academic/Batch/Index', [
            'Batches' => BatchResource::collection($batch),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBatchRequest $request): RedirectResponse
    {
        CreateBatchAction::run($request->validated());

        return back()->with('success', 'Batch created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $batch): Response
    {
        return Inertia::render('Academic/Batch/Show', [
            'batch' => BatchResource::make($batch->load(['program', 'classrooms'])),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBatchRequest $request, Batch $batch): RedirectResponse
    {
        UpdateBatchAction::run($batch, $request->validated());

        return back()->with('success', 'Batch updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batch $batch): RedirectResponse
    {
        DeleteBatchAction::run($batch);

        return back()->with('success', 'Batch deleted successfully');
    }
}
