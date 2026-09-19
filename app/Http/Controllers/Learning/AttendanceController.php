<?php

namespace App\Http\Controllers\Learning;

use App\Actions\Action\Academic\Attendance\GetAttendaceSummaryAction;
use App\Actions\Action\Academic\Attendance\RecordAttendaceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\Attendance\RecordAttendanceRequest;
use App\Http\Resources\Learning\Attendance\AttendanceSummaryResource;
use App\Models\Academic\Classroom;
use App\Models\Learning\ClassSchedule;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function attendanceSummary(
        Classroom $classroom,
        GetAttendaceSummaryAction $action
    ): Response {
        $summaries = $action->handle($classroom);
        return Inertia::render('Learning/Attendance/Summary', [
            'classroom' => $classroom->only('id', 'name'),
            'summaries' => AttendanceSummaryResource::collection($summaries),
        ]);
    }

    public function recordAttendance(
        RecordAttendanceRequest $request,
        ClassSchedule $schedule,
        RecordAttendaceAction $action
    ): RedirectResponse {
        $action->handle($schedule, $request->validated('attendances'));

        return to_route('schedules.show', $schedule)
            ->with('success', 'Attendance successfully recorded.');
    }
}
