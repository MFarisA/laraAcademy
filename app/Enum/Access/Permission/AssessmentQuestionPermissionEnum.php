<?php

namespace App\Enum\Access\Permission;

use App\Interface\Access\Permission\PermissionInterface;
use Override;

enum AssessmentQuestionPermissionEnum: string implements PermissionInterface
{
    case QUESTION_VIEW = 'assessment.question.view';
    case QUESTION_MANAGE = 'assessment.question.manage';

    #[Override]
    public function label(): string
    {
        return match ($this) {
            self::QUESTION_VIEW => 'View Any Questions',
            self::QUESTION_MANAGE => 'Manage Question & Options',
        };
    }

    #[Override]
    public static function group(): string
    {
        return 'Assessment - Question Bank';
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
