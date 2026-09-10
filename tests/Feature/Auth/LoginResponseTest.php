<?php

use App\Enum\Access\RoleRegistryEnum;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate(RoleRegistryEnum::SUPERADMIN->value);
    Role::findOrCreate(RoleRegistryEnum::ADMINBRANCH->value);
    Role::findOrCreate(RoleRegistryEnum::INSTRUCTOR->value);
    Role::findOrCreate(RoleRegistryEnum::STUDENT->value);
});

test('super admin is redirected to admin dashboard after login', function () {
    $user = User::factory()->create(['password' => 'password']);
    $user->assignRole(RoleRegistryEnum::SUPERADMIN->value);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('admin.dashboard'));
});

test('admin branch is redirected to admin dashboard after login', function () {
    $user = User::factory()->create(['password' => 'password']);
    $user->assignRole(RoleRegistryEnum::ADMINBRANCH->value);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('admin.dashboard'));
});

test('instructor is redirected to schedules page after login', function () {
    $user = User::factory()->create(['password' => 'password']);
    $user->assignRole(RoleRegistryEnum::INSTRUCTOR->value);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('schedules.index'));
});

test('student is redirected to student dashboard after login', function () {
    $user = User::factory()->create(['password' => 'password']);
    $user->assignRole(RoleRegistryEnum::STUDENT->value);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard'));
});

test('json login request returns two factor false json', function () {
    $user = User::factory()->create(['password' => 'password']);

    $response = $this->postJson(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk();
    $response->assertJson(['two_factor' => false]);
});
