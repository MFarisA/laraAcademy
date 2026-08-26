<?php

namespace App\Enum\Access\Permission;

use App\Interface\Access\Permission\PermissionInterface;
use Override;

use function array_map;

enum UserPermissionEnum: string implements PermissionInterface
{
    case VIEW_ANY = 'users.view_any';
    case VIEW = 'users.view';
    case CREATE = 'users.create';
    case UPDATE = 'users.update';
    case DELETE = 'users.delete';
    case ASSIGN_ROLE = 'users.assign_role';

    #[Override]
    public function label(): string
    {
        return match ($this) {
            self::VIEW_ANY => 'View Any User',
            self::VIEW => 'View User Detail',
            self::CREATE => 'Create New User',
            self::UPDATE => 'Update User',
            self::DELETE => 'Delete User',
            self::ASSIGN_ROLE => 'Assign Role to User',
        };
    }

    #[Override]
    public static function group(): string
    {
        return 'User Management';
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
