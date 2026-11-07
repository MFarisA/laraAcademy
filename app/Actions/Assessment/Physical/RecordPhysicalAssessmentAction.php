<?php

namespace App\Actions\Assessment\Physical;

use App\Enum\Assessment\PhysicalMetricEnum;
use App\Models\Assessment\Physical\PhysicalAssessment;
use App\Models\Assessment\Physical\PhysicalTestScore;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RecordPhysicalAssessmentAction
{
    use AsAction;

    /**
     * @param  array<string, float|int|string>  $metricScores  Format: ['run_12_minutes' => 3100, 'pull_up' => 14, ...]
     */
    public function handle(
        User $student,
        User $evaluator,
        string $assessmentDate,
        array $metricScores,
        ?string $notes = null,
    ): PhysicalAssessment {
        return DB::transaction(function () use ($student, $evaluator, $assessmentDate, $metricScores, $notes) {
            $assessment = PhysicalAssessment::create([
                'student_id' => $student->id,
                'evaluator_id' => $evaluator->id,
                'assessment_date' => $assessmentDate,
                'total_physical_score' => 0.00,
                'notes' => $notes,
            ]);

            $calculatedScores = [];
            $scorePayload = [];

            foreach (PhysicalMetricEnum::cases() as $metric) {
                if (isset($metricScores[$metric->value])) {
                    $rawValue = (float) $metricScores[$metric->value];
                    $calculatedScore = $metric->calculateScore($rawValue);

                    $scorePayload[] = [
                        'physical_assessment_id' => $assessment->id,
                        'metric_name' => $metric->value,
                        'raw_value' => (string) $rawValue,
                        'calculated_score' => $calculatedScore,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $calculatedScores[] = $calculatedScore;
                }
            }

            if (! empty($scorePayload)) {
                PhysicalTestScore::insert($scorePayload);
            }

            $totalScore = ! empty($calculatedScores)
                ? round(\array_sum($calculatedScores) / \count($calculatedScores), 2)
                : 0.00;

            $assessment->update([
                'total_physical_score' => $totalScore,
            ]);

            return $assessment->fresh('testScores');
        });
    }
}
