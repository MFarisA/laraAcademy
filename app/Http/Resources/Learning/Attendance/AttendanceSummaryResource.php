<?php

namespace App\Http\Resources\Learning\Attendance;

use App\DTO\Learning\Attendance\AttendanceSummaryData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AttendanceSummaryData
 */
class AttendanceSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'student_id' => $this->studentId,
            'student_name' => $this->studentName,
            'total_sessions' => $this->totalSessions,
            'present_count' => $this->presentCount,
            'absent_count' => $this->absentCount,
            'sick_count' => $this->sickCount,
            'permitted_count' => $this->permittedCount,
            'attendance_percentage' => $this->attendancePercentage,
        ];
    }
}
