<?php

namespace App\Enum\Assessment;

enum ExamTemplateTypeEnum: string
{
    case SKD = 'skd';
    case SNBT = 'snbt';
    case KEDINASAN = 'kedinasan';
    case SIMULATION = 'simulation';
}
