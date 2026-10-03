<?php

namespace App\Http\Controllers\Academic;

use App\Actions\Academic\SyncProgramSubjectsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Program\SyncProgramSubjectsRequest;
use App\Models\Academic\Program;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ProgramSubjectController extends Controller
{
    public function update(
        SyncProgramSubjectsRequest $request,
        Program $programs,
        SyncProgramSubjectsAction $action
    ): RedirectResponse {
        $action->handle($programs, $request->validated('subjects', []));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Program subjects successfully updated']);

        return back();
    }
}
