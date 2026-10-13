<?php

namespace Database\Seeders;

use App\Models\Academic\Batch;
use App\Models\Academic\Classroom;
use App\Models\Organization\Branch;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $batches = Batch::all();
        $branches = Branch::all();

        $classNames = ['Garuda A', 'Garuda B', 'Bhayangkara 1', 'Adhyaksa Reguler'];

        foreach ($branches as $branch) {
            foreach ($batches as $batch) {
                foreach (array_slice($classNames, 0, 2) as $cName) {
                    Classroom::firstOrCreate(
                        [
                            'batch_id' => $batch->id,
                            'branch_id' => $branch->id,
                            'name' => "Kelas {$cName} ({$branch->city})",
                        ],
                        [
                            'capacity' => 30,
                        ]
                    );
                }
            }
        }
    }
}
