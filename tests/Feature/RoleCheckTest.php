<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
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

test('new users register will be assigned as Student', function () {
    $this->seed(RolePermissionSeeder::class);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->first();
    expect($user->hasRole(RoleRegistryEnum::STUDENT->value))->toBeTrue();
});
