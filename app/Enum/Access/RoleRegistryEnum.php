<?php

namespace App\Enum\Access;

use Override;

use function array_column;

enum RoleRegistryEnum: string
{
    case STUDENT = 'student';
    case INSTRUCTOR = 'instructor';
    case ADMINBRANCH = 'admin-branch';
    case SUPERADMIN = 'super-admin';
    case EVALUATOR = 'evaluator';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Administrator',
            self::INSTRUCTOR => 'Instructor',
            self::ADMINBRANCH => 'Admin Branch',
            self::EVALUATOR => 'Evaluator',
            self::STUDENT => 'Student',
        };
    }

    /**
     * @return array<mixed>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
