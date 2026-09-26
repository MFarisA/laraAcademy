<?php

namespace App\Enum\Access\Permission;

use App\Interface\Access\Permission\PermissionInterface;
use Override;

enum LearningPermissionEnum: string implements PermissionInterface
{
    case SCHEDULES_VIEW = 'learning.schedules.view';
    case SCHEDULES_MANAGE = 'learning.schedules.manage';
    case ATTENDANCE_VIEW = 'learning.attendances.view';
    case ATTENDANCE_SUBMIT = 'learning.attendances.submit';
    case ATTENDANCE_VERIFY = 'learning.attendances.verify';
    case MATERIALS_VIEW = 'learning.materials.view';
    case MATERIALS_MANAGE = 'learning.materials.manage';
    case MATERIALS_DOWNLOAD = 'learning.materials.download';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULES_VIEW => 'View Class Schedules',
            self::SCHEDULES_MANAGE => 'Manage Class Schedules',
            self::ATTENDANCE_VIEW => 'View Attendance List',
            self::ATTENDANCE_SUBMIT => 'Submit Student Attendance',
            self::ATTENDANCE_VERIFY => 'Verify Attendance',
            self::MATERIALS_VIEW => 'View Learning Materials',
            self::MATERIALS_MANAGE => 'Manage Learning Materials',
            self::MATERIALS_DOWNLOAD => 'Download Learning Materials',
        };
    }

    #[Override]
    public static function group(): string
    {
        return 'Learnings & Class';
    }

    #[Override]
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
