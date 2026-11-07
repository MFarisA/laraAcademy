<?php

namespace App\Http\Resources\Assessment\Physical;

use App\Enum\Assessment\PhysicalMetricEnum;
use App\Models\Assessment\Physical\PhysicalTestScore;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PhysicalTestScore
 */
class PhysicalTestScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $metricEnum = PhysicalMetricEnum::tryFrom($this->metric_name);

        return [
            'id' => $this->id,
            'metric_name' => $this->metric_name,
            'metric_label' => $this->$metricEnum?->label() ?? $this->metric_name,
            'unit' => $metricEnum?->unit() ?? '',
            'raw_value' => $this->raw_value,
            'calculated_score' => (float) $this->calculated_score,
        ];
    }
}
