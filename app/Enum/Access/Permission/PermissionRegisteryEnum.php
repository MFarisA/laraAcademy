<?php

namespace App\Enum\Access\Permission;

use PHPStan\Rules\Functions\SortParameterCastableToStringRule;

enum PermissionRegisteryEnum
{
    /**
     * @return array{class-string<UserPermissionEnum>, class-string<OrganizationPermissionEnum>, class-string<AcademicPermissionEnum>, class-string<LearningPermissionEnum>, class-string<AssessmentQuestionPermissionEnum>, class-string<AssessmentExamPermissionEnum>, class-string<AssessmentPhysicalPermissionEnum>}
     */
    public static function enums(): array
    {
        return [
            UserPermissionEnum::class,
            OrganizationPermissionEnum::class,
            AcademicPermissionEnum::class,
            LearningPermissionEnum::class,
            AssessmentQuestionPermissionEnum::class,
            AssessmentExamPermissionEnum::class,
            AssessmentPhysicalPermissionEnum::class,
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function all(): array
    {
        $permission = [];

        foreach (self::enums() as $enumClass) {
            $permission = array_merge(
                $permission,
                array_column($enumClass::cases(), 'value')
            );
        }
        return $permission;
    }

    /**
     * @return list<array{group: string, permissions: array<mixed>}>
     */
    public static function groupedForUi(): array
    {
        $grouped = [];
        foreach (self::enums() as $enumClass) {
            $grouped[] = [
                'group' => $enumClass::group(),
                'permissions' => $enumClass::toArray(),
            ];
        }
        return $grouped;
    }
}
