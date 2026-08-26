<?php

namespace App\Enum\Access\Permission;

use App\Interface\Access\Permission\PermissionInterface;
use Override;

enum OrganizationPermissionEnum: string implements PermissionInterface
{
    case BRANCH_VIEW_ANY = 'branches.view_any';
    case BRANCH_VIEW = 'branches.view';
    case BRANCH_CREATE = 'branches.create';
    case BRANCH_UPDATE = 'branches.update';
    case BRANCH_DELETE = 'branches.delete';

    #[Override]
    public function label(): string
    {
        return match ($this) {
            self::BRANCH_VIEW_ANY => 'View Any Branches',
            self::BRANCH_VIEW => 'View Branches Details',
            self::BRANCH_CREATE => 'Create New Branches',
            self::BRANCH_UPDATE => 'Update Branches',
            self::BRANCH_DELETE => 'Delete Branches',
        };
    }

    #[Override]
    public static function group(): string
    {
        return 'Branch Management';
    }

    #[Override]
    public static function toArray(): array
    {
        return array_map(
            fn(self $permission) => [
                'value' => $permission->value,
                'label' => $permission->label(),
            ],
            self::cases()
        );
    }
}
