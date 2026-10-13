<?php

namespace Database\Factories\Academic;

use App\Models\Academic\Batch;
use App\Models\Academic\Classroom;
use App\Models\Organization\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'batch_id' => Batch::factory(),
            'branch_id' => Branch::factory(),
            'name' => 'Kelas '.fake()->randomElement(['Garuda', 'Adhyaksa', 'Bhayangkara', 'Prajurit', 'Wiyata']).' '.fake()->randomElement(['A', 'B', 'C', '1', '2']),
            'capacity' => fake()->numberBetween(20, 35),
        ];
    }
}
