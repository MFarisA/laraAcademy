<?php

namespace App\DTO\Learning\Attendance;

final readonly class AttendanceSummaryData
{
    public function __construct(
        public int $studentId,
        public string $studentName,
        public int $totalSessions,
        public int $presentCount,
        public int $absentCount,
        public int $sickCount,
        public int $permittedCount,
        public float $attendancePercentage,
    ) {}
}
