<?php

use App\Enum\Access\Permission\UserPermissionEnum;
use App\Enum\Access\RoleRegistryEnum;
use App\Models\Academic\Batch;
use App\Models\Academic\Classroom;
use App\Models\Academic\Program;
use App\Models\Organization\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Context;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->withoutVite();
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    Role::findOrCreate(RoleRegistryEnum::ADMINBRANCH->value);
    Permission::findOrCreate(UserPermissionEnum::VIEW->value);
});

test('branch id is set in context when user has branch', function () {
    $branch = Branch::factory()->create();
    $user = User::factory()->create(['branch_id' => $branch->id]);
    $user->givePermissionTo(UserPermissionEnum::VIEW->value);

    $this->actingAs($user)->get(route('users.index'));

    expect(Context::get('branch_id'))->toBe($branch->id);
});

test('admin branch without branch assignment is forbidden from branch context routes', function () {
    $adminBranch = User::factory()->create(['branch_id' => null]);
    $adminBranch->assignRole(RoleRegistryEnum::ADMINBRANCH->value);
    $adminBranch->givePermissionTo(UserPermissionEnum::VIEW->value);

    $response = $this->actingAs($adminBranch)->get(route('users.index'));

    $response->assertForbidden();
});

test('admin branch with branch assignment can access branch context routes', function () {
    $branch = Branch::factory()->create();
    $adminBranch = User::factory()->create(['branch_id' => $branch->id]);
    $adminBranch->assignRole(RoleRegistryEnum::ADMINBRANCH->value);
    $adminBranch->givePermissionTo(UserPermissionEnum::VIEW->value);

    $response = $this->actingAs($adminBranch)->get(route('users.index'));

    $response->assertOk();
});

test('super admin without branch assignment can access branch context routes', function () {
    $superAdmin = User::factory()->create(['branch_id' => null]);
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $response = $this->actingAs($superAdmin)->get(route('users.index'));

    $response->assertOk();
});

test('accessibleBy scope filters records by branch for regular users', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();

    $userA = User::factory()->create(['branch_id' => $branchA->id]);
    $userB = User::factory()->create(['branch_id' => $branchB->id]);

    $scopedUsers = User::accessibleBy($userA)->get();

    expect($scopedUsers->pluck('id')->all())->toContain($userA->id)
        ->and($scopedUsers->pluck('id')->all())->not->toContain($userB->id);
});

test('accessibleBy scope does not filter records for super admin', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();

    $superAdmin = User::factory()->create(['branch_id' => null]);
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $userA = User::factory()->create(['branch_id' => $branchA->id]);
    $userB = User::factory()->create(['branch_id' => $branchB->id]);

    $scopedUsers = User::accessibleBy($superAdmin)->get();

    expect($scopedUsers->pluck('id')->all())->toContain($userA->id)
        ->and($scopedUsers->pluck('id')->all())->toContain($userB->id);
});

test('belongsToBranch trait works on classroom model', function () {
    $branchA = Branch::factory()->create();
    $branchB = Branch::factory()->create();

    $program = Program::create(['name' => 'Kedinasan', 'slug' => 'kedinasan']);
    $batch = Batch::create(['name' => 'Batch 2026', 'program_id' => $program->id]);

    $classroomA = Classroom::create([
        'name' => 'Kelas A',
        'branch_id' => $branchA->id,
        'batch_id' => $batch->id,
        'capacity' => 30,
    ]);
    $classroomB = Classroom::create([
        'name' => 'Kelas B',
        'branch_id' => $branchB->id,
        'batch_id' => $batch->id,
        'capacity' => 30,
    ]);

    $userA = User::factory()->create(['branch_id' => $branchA->id]);

    $scopedClassrooms = Classroom::accessibleBy($userA)->get();

    expect($scopedClassrooms->pluck('id')->all())->toContain($classroomA->id)
        ->and($scopedClassrooms->pluck('id')->all())->not->toContain($classroomB->id);
});
