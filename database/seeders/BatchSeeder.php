<?php

namespace Database\Seeders;

use App\Models\Academic\Batch;
use App\Models\Academic\Program;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = Program::all();

        foreach ($programs as $program) {
            Batch::firstOrCreate(
                [
                    'program_id' => $program->id,
                    'name' => 'Angkatan 1 - 2026',
                ],
                [
                    'start_date' => now()->subMonth()->format('Y-m-d'),
                    'end_date' => now()->addMonths(3)->format('Y-m-d'),
                ]
            );

            Batch::firstOrCreate(
                [
                    'program_id' => $program->id,
                    'name' => 'Angkatan 2 - 2026',
                ],
                [
                    'start_date' => now()->format('Y-m-d'),
                    'end_date' => now()->addMonths(4)->format('Y-m-d'),
                ]
            );
        }
    }
}
