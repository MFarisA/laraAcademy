<?php

namespace App\Enum\Assessment;

enum GradingRuleEnum: string
{
    case STANDARD = 'STANDARD';
    case TKP = 'TKP';

    public function label(): string
    {
        return match ($this) {
            self::STANDARD => 'standard',
            self::TKP => 'tkp',
        };
    }
}
