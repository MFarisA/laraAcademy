<?php

namespace App\Actions\Academic\Attendance;

use App\DTO\Learning\Attendance\AttendanceSummaryData;
use App\Enum\Learning\AttendanceStatusEnum;
use App\Models\Academic\Classroom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAttendanceSummaryAction
{
    use AsAction;

    /**
     * @return Collection<int, AttendanceSummaryData>
     */
    public function handle(Classroom $classroom): Collection
    {
        return $classroom->students()->withCount([
            'attendances as total_sessions' => function (Builder $query) use ($classroom) {
                $query->whereRelation('schedule', 'classroom_id', $classroom->id);
            },
            'attendances as present_count' => function (Builder $query) use ($classroom) {
                $query->whereRelation('schedule', 'classroom_id', $classroom->id)
                    ->where('status', AttendanceStatusEnum::PRESENT);
            },
            'attendances as absent_count' => function (Builder $query) use ($classroom) {
                $query->whereRelation('schedule', 'classroom_id', $classroom->id)
                    ->where('status', AttendanceStatusEnum::ABSENT);
            },
            'attendances as sick_count' => function (Builder $query) use ($classroom) {
                $query->whereRelation('schedule', 'classroom_id', $classroom->id)
                    ->where('status', AttendanceStatusEnum::SICK);
            },
            'attendances as permitted_count' => function (Builder $query) use ($classroom) {
                $query->whereRelation('schedule', 'classroom_id', $classroom->id)
                    ->where('status', AttendanceStatusEnum::PERMITTED);
            },
        ])
            ->get()
            ->map(function ($student): AttendanceSummaryData {
                $totalSessions = (int) ($student->getAttribute('total_sessions') ?? 0);
                $presentCount = (int) ($student->getAttribute('present_count') ?? 0);
                $absentCount = (int) ($student->getAttribute('absent_count') ?? 0);
                $sickCount = (int) ($student->getAttribute('sick_count') ?? 0);
                $permittedCount = (int) ($student->getAttribute('permitted_count') ?? 0);

                return new AttendanceSummaryData(
                    studentId: $student->id,
                    studentName: $student->name,
                    totalSessions: $totalSessions,
                    presentCount: $presentCount,
                    absentCount: $absentCount,
                    sickCount: $sickCount,
                    permittedCount: $permittedCount,
                    attendancePercentage: $totalSessions > 0
                        ? round(($presentCount / $totalSessions) * 100, 2)
                        : 0.0,
                );
            });
    }
}
