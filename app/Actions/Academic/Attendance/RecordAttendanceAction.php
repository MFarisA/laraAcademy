<?php

namespace App\Actions\Academic\Attendance;

use App\Models\Learning\Attendance;
use App\Models\Learning\ClassSchedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RecordAttendanceAction
{
    use AsAction;

    /**
     * @param  array<mixed>  $data
     * @return Collection<int, Attendance>
     */
    public function handle(ClassSchedule $schedule, array $data): Collection
    {
        return DB::transaction(function () use ($schedule, $data) {
            $now = now();
            $records = new Collection;

            foreach ($data as $row) {
                $attendance = Attendance::updateOrCreate(
                    [
                        'class_schedule_id' => $schedule->id,
                        'student_id' => $row['student_id'],
                    ],
                    [
                        'status' => $row['status'],
                        'verified_at' => $now,
                    ],
                );
                $records->push($attendance);
            }

            return $records;
        });
    }
}
