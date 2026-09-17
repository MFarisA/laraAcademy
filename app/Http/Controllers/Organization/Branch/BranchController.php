<?php

namespace App\Http\Controllers\Organization\Branch;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Branch\StoreBranchRequest;
use App\Http\Requests\Organization\Branch\UpdateBranchRequest;
use App\Http\Resources\Organization\Branch\BranchResource;
use App\Models\Organization\Branch;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Branches/Index', [
            'branches' => BranchResource::collection(
                Branch::query()
                    ->latest()
                    ->get()
            ),
        ]);
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        Branch::create($request->validated());

        return to_route('branches.index')->with('success', 'Branch successfully created.');
    }

    public function show(Branch $branch): Response
    {
        return Inertia::render('branches/Show', [
            'branches' => new BranchResource($branch),
        ]);
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $branch->update($request->validated());

        return to_route('branches.index')->with('success', 'Branch successfully updated.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return to_route('branches.index')->with('success', 'Branch successfully deleted.');
    }
}
