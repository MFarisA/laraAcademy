<?php

namespace App\Http\Controllers\Academic;

use App\Actions\Academic\SyncProgramSubjectsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Program\SyncProgramSubjectsRequest;
use App\Models\Academic\Program;
use Illuminate\Http\RedirectResponse;

class ProgramSubjectController extends Controller
{
    public function update(
        SyncProgramSubjectsRequest $request,
        Program $programs,
        SyncProgramSubjectsAction $action
    ): RedirectResponse {
        $action->handle($programs, $request->validated('subjects', []));

        return back()->with('success', 'Program subjects successfully updated');
    }
}
