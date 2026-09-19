<?php

namespace App\Enum\Learning;

enum AttendanceStatusEnum: string
{
    case PRESENT = 'present';
    case SICK = 'sick';
    case ABSENT = 'absent';
    case PERMITTED = 'permitted';

    public function label(): string
    {
        return match ($this) {
            self::PRESENT => 'hadir',
            self::SICK => 'sakit',
            self::ABSENT => 'tidak hadir',
            self::PERMITTED => 'izin',
        };
    }
}
