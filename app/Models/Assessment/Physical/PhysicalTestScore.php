<?php

namespace App\Models\Assessment\Physical;

use Database\Factories\Assessment\Physical\PhysicalTestScoreFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'physical_assessment_id',
    'metric_name',
    'raw_value',
    'calculated_score',
])]
class PhysicalTestScore extends Model
{
    /** @use HasFactory<PhysicalTestScoreFactory> */
    use HasFactory;

    #[Override]
    public function casts()
    {
        return [
            'calculated_score' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<PhysicalAssessment, $this>
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(PhysicalAssessment::class, 'physical_assessment_id');
    }
}
