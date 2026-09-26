<?php

namespace App\Enum\Access\Permission;

use App\Interface\Access\Permission\PermissionInterface;
use Override;

use function array_map;

enum AcademicPermissionEnum: string implements PermissionInterface
{
    case PROGRAMS_MANAGE = 'academics.programs.manage';
    case SUBJECTS_MANAGE = 'academics.subjects.manage';
    case BATCHES_MANAGE = 'academics.batches.manage';
    case CLASSROOM_VIEW = 'academics.classrooms.view';
    case CLASSROOM_MANAGE = 'academics.classrooms.manage';
    case ENROLLMENTS_MANAGE = 'academics.enrollments.manage';

    // [Override]
    public function label(): string
    {
        return match ($this) {
            self::PROGRAMS_MANAGE => 'Manage Academic Program',
            self::SUBJECTS_MANAGE => 'Manage Subjects',
            self::BATCHES_MANAGE => 'Manage Batches',
            self::CLASSROOM_VIEW => 'View Classrooms',
            self::CLASSROOM_MANAGE => 'Manage Classrooms',
            self::ENROLLMENTS_MANAGE => 'Manage Program',
        };
    }

    #[Override]
    public static function group(): string
    {
        return 'Academic';
    }

    // #[Override]
    public static function toArray(): array
    {
        return array_map(
            fn (self $permission) => [
                'value' => $permission->value,
                'label' => $permission->label(),
            ],
            self::cases()
        );
    }
}
