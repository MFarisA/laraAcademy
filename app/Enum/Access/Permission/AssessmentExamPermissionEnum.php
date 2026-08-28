<?php

namespace App\Enum\Access\Permission;

use App\Interface\Access\Permission\PermissionInterface;
use Override;

enum AssessmentExamPermissionEnum: string implements PermissionInterface
{
    case TEMPLATES_MANAGE = 'assessment.exam_templates.manage';
    case SESSIONS_VIEW = 'assessment.exam_sessions.view';
    case SESSIONS_MANAGE = 'assessment.exam_sessions.manage';
    case ATTEMPTS_TAKE = 'assessment.exam_attempts.take';
    case ATTEMPTS_VIEW_RESULT = 'assessment.exam_attempts.view_result';
    case ATTEMPTS_GRADE_MANUAL = 'assessment.exam_attempts.grade_manual';

    #[Override]
    public function label(): string
    {
        return match ($this) {
            self::TEMPLATES_MANAGE => 'Manage Exam Templates and Sections',
            self::SESSIONS_VIEW => 'View Exam Sessions',
            self::SESSIONS_MANAGE => 'Manage Exam Sessions and Tokens',
            self::ATTEMPTS_TAKE => 'Take Exam Attempt',
            self::ATTEMPTS_VIEW_RESULT => 'View Exam Results and Rankings',
            self::ATTEMPTS_GRADE_MANUAL => 'Grade Manual Exam Answers',
        };
    }

    #[Override]
    public static function group(): string
    {
        return 'Assessment - CBT / Exam';
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
