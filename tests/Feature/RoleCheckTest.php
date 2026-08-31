<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

test('super admin can bypass any permission check via gate', function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    expect(Gate::forUser($superAdmin)->allows('random.unregistered.permission'))->toBeTrue();
});

test('regular user cannot bypass unregistered permission', function () {
    $regularUser = User::factory()->create();
    expect(Gate::forUser($regularUser)->allows('random.unregistered.permission'))->toBeFalse();
});
