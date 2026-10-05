<?php

namespace App\Enum\Assessment;

enum DifficultyLevelEnum: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function label(): string
    {
        return match ($this) {
            self::EASY => 'Mudah',
            self::MEDIUM => 'Sedang',
            self::HARD => 'Sulit'
        };
    }
}
