<?php

namespace App\Enum\Assessment;

enum ExamAttemptStatusEnum: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';
}
