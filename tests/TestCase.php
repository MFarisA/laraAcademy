<?php

namespace Tests;

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    /**
     * Sebagian besar fitur (registrasi, dashboard, setting) bergantung pada
     * role & permission yang di-seed. Karena RefreshDatabase hanya menjalankan
     * migrasi, kita seed role & permission di sini agar RBAC berfungsi di test
     * (mis. CreateNewUser::assignRole('student')).
     *
     * Seeder-nya idempotent, jadi aman dijalankan di setiap test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
}
