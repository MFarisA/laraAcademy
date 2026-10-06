<?php

namespace App\Enum\Assessment;

enum ExamAttemptStatusEnum: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'Sedang Progres',
            self::COMPLETED => 'Selesai',
            self::EXPIRED => 'Expired',
        };
    }
}
