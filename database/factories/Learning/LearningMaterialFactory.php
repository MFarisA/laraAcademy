<?php

namespace Database\Factories\Learning;

use App\Models\Academic\Subject;
use App\Models\Learning\LearningMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningMaterial>
 */
class LearningMaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['pdf', 'video', 'link']);

        return [
            'subject_id' => Subject::factory(),
            'title' => 'Modul Pendalaman '.fake()->words(3, true),
            'type' => $type,
            'file_url' => $type === 'link' ? fake()->url() : 'https://storage.laraacademy.id/materials/sample_'.fake()->slug().'.pdf',
            'is_downloadable' => fake()->boolean(85),
        ];
    }
}
