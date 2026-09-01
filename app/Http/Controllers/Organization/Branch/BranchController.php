<?php

namespace App\Http\Controllers\Organization\Branch;

use App\Actions\Action\Organization\Branch\DeleteBranchAction;
use App\Actions\Action\Organization\Branch\StoreBranchAction;
use App\Actions\Action\Organization\Branch\UpdateBranchAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Branch\StoreBranchRequest;
use App\Http\Requests\Organization\Branch\UpdateBranchRequest;
use App\Http\Resources\Organization\Branch\StoreBranchResource;
use App\Models\Organization\Branch;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Branches/Index', [
            'branches' => StoreBranchResource::collection(
                Branch::query()
                    ->latest()
                    ->get()
            ),
        ]);
    }
    public function store(StoreBranchRequest $request, StoreBranchAction $storeBranch): RedirectResponse
    {
        $storeBranch->handle($request->validated());
        return to_route('branches.index')->with('success', 'Branch successfully created.');
    }

    public function show(Branch $branch): Response
    {
        return Inertia::render('branches/Show', [
            'branches' => new StoreBranchResource($branch),
        ]);
    }

    public function update(UpdateBranchRequest $request, Branch $branch, UpdateBranchAction $updateBranch): RedirectResponse
    {
        $data = $request->validated();
        $updateBranch->handle($branch, $data);
        return to_route('branches.index')->with('success', 'Branch successfully updated.');
    }

    public function destroy(DeleteBranchAction $deleteBranch, Branch $branch): RedirectResponse
    {
        $deleteBranch->handle($branch);

        session()->flash('success', 'Branch successfully deleted.');

        return to_route('branches.index');
    }
}
