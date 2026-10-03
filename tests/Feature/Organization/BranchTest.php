
<?php

use App\Models\Organization\Branch;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::findOrCreate('super-admin');
    $this->superAdmin = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->superAdmin->assignRole('super-admin');
});

test('test create branch', function () {
    $payload = [
        'name' => 'Cabang Jakarta Selatan',
        'code' => 'JKT-01',
        'city' => 'Jakarta',
        'address' => 'Jl. Sudirman No. 123',
    ];
    $response = $this->actingAs($this->superAdmin)->post(route('branches.store'), $payload);
    $response->assertRedirect(route('branches.index'));
    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Branch successfully created.'],
    ]);

    $this->assertDatabaseHas('branches', [
        'name' => 'Cabang Jakarta Selatan',
        'code' => 'JKT-01',
    ]);
});

test('unauthenticated user cannot store a branch', function () {
    $response = $this->post(route('branches.store'), [
        'name' => 'New Branches',
    ]);
    $response->assertRedirect(route('login'));
});

// test('unverified user cannot store a branch', function () {
//     $unverifiedUser = User::factory()->unverified()->create();
//     $unverifiedUser->assignRole('super-admin');

//     $response = $this->actingAs($unverifiedUser)
//         ->post(route('branches.store'), []);

//     $response->assertRedirect(route('verification.notice'));
// });

test('non super-admin user is forbidden from storing a branch', function () {
    $regularUser = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($regularUser)
        ->post(route('branches.store'), []);

    $response->assertForbidden();
});

test('super admin can update a branch', function () {
    $branch = Branch::factory()->create([
        'name' => 'Cabang Lama',
        'code' => 'OLD-01',
    ]);

    $payload = [
        'name' => 'Cabang Baru Diperbarui',
        'code' => 'NEW-01',
        'city' => 'Bandung',
        'address' => 'Jl. Asia Afrika No. 45',
    ];

    $response = $this->actingAs($this->superAdmin)
        ->put(route('branches.update', $branch), $payload);

    $response->assertRedirect(route('branches.index'));
    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Branch successfully updated.'],
    ]);

    $this->assertDatabaseHas('branches', [
        'id' => $branch->id,
        'name' => 'Cabang Baru Diperbarui',
        'code' => 'NEW-01',
    ]);
});

test('super admin can delete a branch', function () {
    $branch = Branch::factory()->create();

    $response = $this->actingAs($this->superAdmin)
        ->delete(route('branches.destroy', $branch));

    $response->assertRedirect(route('branches.index'));
    $response->assertSessionHas('inertia.flash_data', [
        'toast' => ['type' => 'success', 'message' => 'Branch successfully deleted.'],
    ]);

    // Jika model menggunakan SoftDeletes, ganti dengan assertSoftDeleted
    $this->assertDatabaseMissing('branches', [
        'id' => $branch->id,
    ]);
});
