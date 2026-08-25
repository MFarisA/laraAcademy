<?php

namespace App\Models\Assessment\Physical;

use App\Models\User;
use Database\Factories\Assessment\Physical\PhysicalAssessmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable([
    'student_id',
    'evaluator_id',
    'assessment_date',
    'total_physical_score',
    'notes',
])]
class PhysicalAssessment extends Model
{
    /** @use HasFactory<PhysicalAssessmentFactory> */
    use HasFactory;

    #[Override]
    public function casts()
    {
        return [
            'assessment_date' => 'date',
            'total_physical_score' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * @return HasMany<PhysicalTestScore, $this>
     */
    public function testScores(): HasMany
    {
        return $this->hasMany(PhysicalTestScore::class);
    }
}
