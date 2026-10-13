<?php

namespace Database\Seeders;

use App\Enum\Access\RoleRegistryEnum;
use App\Models\Organization\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branchesData = [
            [
                'name' => 'Cabang Jakarta Pusat',
                'code' => 'JKT-01',
                'city' => 'Jakarta Pusat',
                'address' => 'Jl. Salemba Raya No. 45, Senen, Jakarta Pusat',
            ],
            [
                'name' => 'Cabang Bandung Dago',
                'code' => 'BDG-01',
                'city' => 'Bandung',
                'address' => 'Jl. Ir. H. Djuanda No. 120, Dago, Bandung',
            ],
            [
                'name' => 'Cabang Surabaya Gubeng',
                'code' => 'SBY-01',
                'city' => 'Surabaya',
                'address' => 'Jl. Sumatra No. 28, Gubeng, Surabaya',
            ],
            [
                'name' => 'Cabang Yogyakarta UGM',
                'code' => 'YOG-01',
                'city' => 'Yogyakarta',
                'address' => 'Jl. Kaliurang KM 5, Sleman, D.I. Yogyakarta',
            ],
        ];

        foreach ($branchesData as $bData) {
            Branch::firstOrCreate(['code' => $bData['code']], $bData);
        }

        $jakartaBranch = Branch::where('code', 'JKT-01')->first();
        $bandungBranch = Branch::where('code', 'BDG-01')->first();
        $surabayaBranch = Branch::where('code', 'SBY-01')->first();

        // Akun test praktis dengan password 'x'
        // 1. Admin Cabang
        $adminBranch = User::firstOrCreate(
            ['email' => 'admin@x.com'],
            [
                'name' => 'Budi Santoso (Admin Cabang)',
                'branch_id' => $jakartaBranch?->id,
                'password' => Hash::make('x'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $adminBranch->syncRoles([RoleRegistryEnum::ADMINBRANCH->value]);

        // 2. Instruktur
        $guru = User::firstOrCreate(
            ['email' => 'guru@x.com'],
            [
                'name' => 'Dra. Sri Wahyuni, M.Pd (Instruktur)',
                'branch_id' => $jakartaBranch?->id,
                'password' => Hash::make('x'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $guru->syncRoles([RoleRegistryEnum::INSTRUCTOR->value]);

        // 3. Siswa utama
        $siswa = User::firstOrCreate(
            ['email' => 'siswa@x.com'],
            [
                'name' => 'Ahmad Fikri Pratama (Siswa)',
                'branch_id' => $jakartaBranch?->id,
                'password' => Hash::make('x'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $siswa->syncRoles([RoleRegistryEnum::STUDENT->value]);

        // Buat beberapa instruktur tambahan per cabang
        foreach ([$jakartaBranch, $bandungBranch, $surabayaBranch] as $br) {
            if (! $br) {
                continue;
            }

            for ($i = 1; $i <= 2; $i++) {
                $instructor = User::factory()->create([
                    'branch_id' => $br->id,
                    'name' => fake()->name().', S.Pd',
                    'is_active' => true,
                ]);
                $instructor->syncRoles([RoleRegistryEnum::INSTRUCTOR->value]);
            }

            // Buat 8 siswa tambahan per cabang
            for ($s = 1; $s <= 8; $s++) {
                $studentUser = User::factory()->create([
                    'branch_id' => $br->id,
                    'is_active' => true,
                ]);
                $studentUser->syncRoles([RoleRegistryEnum::STUDENT->value]);
            }
        }
    }
}
