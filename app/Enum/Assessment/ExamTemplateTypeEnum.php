<?php

namespace App\Enum\Assessment;

enum ExamTemplateTypeEnum: string
{
    case SKD = 'skd';
    case SNBT = 'snbt';
    case KEDINASAN = 'kedinasan';
    case SIMULATION = 'simulation';

    public function label(): string
    {
        return match ($this) {
            self::SKD => 'SKD',
            self::SNBT => 'SNBT',
            self::KEDINASAN => 'Kedinasan',
            self::SIMULATION => 'Simulasi'
        };
    }
}
