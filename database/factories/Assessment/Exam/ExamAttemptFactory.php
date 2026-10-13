<?php

namespace Database\Factories\Assessment\Exam;

use App\Enum\Assessment\ExamAttemptStatusEnum;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamAttempt>
 */
class ExamAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 day', 'now');
        $status = fake()->randomElement([
            ExamAttemptStatusEnum::IN_PROGRESS,
            ExamAttemptStatusEnum::COMPLETED,
        ]);
        $isCompleted = $status === ExamAttemptStatusEnum::COMPLETED;

        return [
            'exam_session_id' => ExamSession::factory(),
            'student_id' => User::factory(),
            'started_at' => $startedAt,
            'submitted_at' => $isCompleted ? (clone $startedAt)->modify('+90 minutes') : null,
            'total_score' => $isCompleted ? fake()->randomFloat(2, 280, 475) : 0.00,
            'is_passed' => $isCompleted ? fake()->boolean(70) : false,
            'status' => $status,
        ];
    }
}
