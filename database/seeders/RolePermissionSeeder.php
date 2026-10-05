<?php

namespace Database\Seeders;

use App\Enum\Access\Permission\AcademicPermissionEnum;
use App\Enum\Access\Permission\AssessmentExamPermissionEnum;
use App\Enum\Access\Permission\AssessmentPhysicalPermissionEnum;
use App\Enum\Access\Permission\AssessmentQuestionPermissionEnum;
use App\Enum\Access\Permission\LearningPermissionEnum;
use App\Enum\Access\Permission\PermissionRegisteryEnum;
use App\Enum\Access\Permission\UserPermissionEnum;
use App\Enum\Access\RoleRegistryEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cache Spatie agar role/permission terbaru dibaca
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');
        $now = now();

        // 2. Insert semua permission dari Enum Registry secara massal (idempotent)
        $permissions = array_map(fn (string $name) => [
            'name' => $name,
            'guard_name' => $guard,
            'created_at' => $now,
            'updated_at' => $now,
        ], PermissionRegisteryEnum::all());

        DB::table(config('permission.table_names.permissions'))->insertOrIgnore($permissions);

        // 3. Mapping role -> daftar permission (nama string dari enum value)
        $rolePermissions = [
            // SUPERADMIN sengaja TIDAK dikasih permission di sini.
            // Akses penuh akan diberikan via Gate::before() di AppServiceProvider,
            // jadi tidak perlu mencatat ribuan baris di role_has_permissions.
            RoleRegistryEnum::SUPERADMIN->value => [],

            RoleRegistryEnum::ADMINBRANCH->value => [
                UserPermissionEnum::VIEW_ANY->value,
                UserPermissionEnum::VIEW->value,
                UserPermissionEnum::CREATE->value,
                UserPermissionEnum::UPDATE->value,
                AcademicPermissionEnum::CLASSROOM_VIEW->value,
                AcademicPermissionEnum::CLASSROOM_MANAGE->value,
                AcademicPermissionEnum::ENROLLMENTS_MANAGE->value,
                LearningPermissionEnum::SCHEDULES_VIEW->value,
                LearningPermissionEnum::SCHEDULES_MANAGE->value,
                LearningPermissionEnum::ATTENDANCE_VIEW->value,
                AssessmentExamPermissionEnum::SESSIONS_VIEW->value,
                AssessmentExamPermissionEnum::SESSIONS_MANAGE->value,
                AssessmentExamPermissionEnum::ATTEMPTS_VIEW_RESULT->value,
                AssessmentPhysicalPermissionEnum::PHYSICAL_VIEW->value,
            ],

            RoleRegistryEnum::INSTRUCTOR->value => [
                AcademicPermissionEnum::CLASSROOM_VIEW->value,
                LearningPermissionEnum::SCHEDULES_VIEW->value,
                LearningPermissionEnum::ATTENDANCE_VIEW->value,
                LearningPermissionEnum::ATTENDANCE_VERIFY->value,
                LearningPermissionEnum::MATERIALS_VIEW->value,
                LearningPermissionEnum::MATERIALS_MANAGE->value,
                LearningPermissionEnum::MATERIALS_DOWNLOAD->value,
                AssessmentQuestionPermissionEnum::QUESTION_VIEW->value,
                AssessmentQuestionPermissionEnum::QUESTION_MANAGE->value,
                AssessmentExamPermissionEnum::SESSIONS_VIEW->value,
                AssessmentExamPermissionEnum::SESSIONS_MANAGE->value,
                AssessmentExamPermissionEnum::ATTEMPTS_VIEW_RESULT->value,
                AssessmentExamPermissionEnum::ATTEMPTS_GRADE_MANUAL->value,
            ],

            RoleRegistryEnum::EVALUATOR->value => [
                AcademicPermissionEnum::CLASSROOM_VIEW->value,
                AssessmentPhysicalPermissionEnum::PHYSICAL_VIEW->value,
                AssessmentPhysicalPermissionEnum::PHYSICAL_ASSESS->value,
                AssessmentPhysicalPermissionEnum::PHYSICAL_MANAGE->value,
            ],

            RoleRegistryEnum::STUDENT->value => [
                LearningPermissionEnum::SCHEDULES_VIEW->value,
                LearningPermissionEnum::ATTENDANCE_SUBMIT->value,
                LearningPermissionEnum::MATERIALS_VIEW->value,
                LearningPermissionEnum::MATERIALS_DOWNLOAD->value,
                AssessmentExamPermissionEnum::ATTEMPTS_TAKE->value,
                AssessmentExamPermissionEnum::ATTEMPTS_VIEW_RESULT->value,
                AssessmentPhysicalPermissionEnum::PHYSICAL_VIEW->value,
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);

            // syncPermissions menerima array nama string permission
            $role->syncPermissions($permissionNames);
        }

        // 4. Akun bootstrap Super Admin
        User::firstOrCreate(
            ['email' => 'a@x.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('x'),
                'email_verified_at' => $now,
            ]
        )->syncRoles([RoleRegistryEnum::SUPERADMIN->value]);
    }
}
