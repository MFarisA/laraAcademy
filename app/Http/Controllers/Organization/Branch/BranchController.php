<?php

namespace App\Http\Controllers\Organization\Branch;

use App\Actions\Action\Organization\Branch\StoreBranchAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\Branch\StoreBranchRequest;
use Illuminate\Http\RedirectResponse;

class BranchController extends Controller
{
    /**
     *
     */
    public function store(StoreBranchRequest $request, StoreBranchAction $storeBranch): RedirectResponse
    {
        $storeBranch->handle($request->validated());
        return to_route('branches.index')->with('success', 'Branch successfully created.');
    }
}
