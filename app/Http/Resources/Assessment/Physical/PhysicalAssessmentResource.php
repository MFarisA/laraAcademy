<?php

namespace App\Http\Resources\Assessment\Physical;

use App\Models\Assessment\Physical\PhysicalAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PhysicalAssessment
 */
class PhysicalAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_date' => $this->assessment_date?->format('Y-m-d'),
            'total_physical_score' => (float) $this->total_physical_score,
            'notes' => $this->notes,
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->student->id,
                    'name' => $this->student->name,
                    'email' => $this->student->email,
                    'branch' => $this->student->branch ? [
                        'id' => $this->student->branch->id,
                        'name' => $this->student->branch->name,
                    ] : null,
                ];
            }),
            'evaluator' => $this->whenLoaded('evaluator', function () {
                return [
                    'id' => $this->evaluator->id,
                    'name' => $this->evaluator->name,
                ];
            }),
            'test_scores' => PhysicalTestScoreResource::collection($this->whenLoaded('testScores')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
