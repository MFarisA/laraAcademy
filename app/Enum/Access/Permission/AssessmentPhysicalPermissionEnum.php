<?php

namespace App\Enum\Access\Permission;

use App\Interface\Access\Permission\PermissionInterface;
use Override;

enum AssessmentPhysicalPermissionEnum: string implements PermissionInterface
{
    case PHYSICAL_VIEW = 'assessment.physical.view';
    case PHYSICAL_ASSESS = 'assessment.physical.assess';
    case PHYSICAL_MANAGE = 'assessment.physical.manage';

    #[Override]
    public function label(): string
    {
        return match ($this) {
            self::PHYSICAL_VIEW => 'View Physical Test Assessments',
            self::PHYSICAL_ASSESS => 'Input / Score Physical Assessment',
            self::PHYSICAL_MANAGE => 'Manage Physical Metrics and Scores',
        };
    }

    #[Override]
    public static function group(): string
    {
        return 'Assessment - Physical';
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
