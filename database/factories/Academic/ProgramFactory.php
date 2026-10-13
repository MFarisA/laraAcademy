<?php

namespace Database\Factories\Academic;

use App\Models\Academic\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Persiapan CPNS & Kedinasan Akbar',
                'Intensif SNBT UTBK 2026',
                'Sekolah Kedinasan IPDN & POLTEKIP',
                'Akpol & Akmil Bintara Camp',
                'Bimbel Kedinasan STAN & STIS',
            ]).' '.fake()->numberBetween(1, 999),
            'description' => fake()->paragraph(),
        ];
    }
}
