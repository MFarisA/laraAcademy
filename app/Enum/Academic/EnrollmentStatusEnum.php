<?php

namespace App\Enum\Academic;

enum EnrollmentStatusEnum: string
{
    case ACTIVE = 'active';
    case FINISHED = 'finished';
    case DROPPED = 'dropped';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::FINISHED => 'Selesai',
            self::DROPPED => 'Keluar/Pindah',
        };
    }
}
