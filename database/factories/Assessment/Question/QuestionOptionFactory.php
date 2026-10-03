<?php

namespace Database\Factories\Assessment\Question;

use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    /**
     * `option_label` sengaja tidak diberi default: label bersifat posisional
     * (A, B, C, D) relatif terhadap satu question, jadi harus dispesifikasikan
     * oleh pemanggil setiap kali.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'option_text' => fake()->sentence(6),
            'is_correct' => false,
            'weight_score' => 0,
        ];
    }

    public function correct(): static
    {
        return $this->state(fn (): array => [
            'is_correct' => true,
            'weight_score' => 1,
        ]);
    }

    public function weighted(float $score): static
    {
        return $this->state(fn (): array => [
            'is_correct' => false,
            'weight_score' => $score,
        ]);
    }
}
