<?php

namespace Database\Factories\Academic;

use App\Models\Academic\Batch;
use App\Models\Academic\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Batch>
 */
class BatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+3 months');

        return [
            'program_id' => Program::factory(),
            'name' => 'Angkatan '.fake()->numberBetween(1, 20).' - '.fake()->year(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];
    }
}
