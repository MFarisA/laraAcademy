<?php

namespace App\Models\Assessment\Physical;

use App\Enum\Access\RoleRegistryEnum;
use App\Models\User;
use Database\Factories\Assessment\Physical\PhysicalAssessmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
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
     * @param  Builder<PhysicalAssessment>  $query
     */
    public function scopeAccessibleBy(Builder $query, ?User $user = null): void
    {
        if (! $user) {
            return;
        }

        if ($user->hasRole(RoleRegistryEnum::STUDENT->value)) {
            $query->where('student_id', $user->id);
        } elseif ($user->hasRole(RoleRegistryEnum::ADMINBRANCH->value) && $user->branch_id !== null) {
            $query->whereHas('student', fn (Builder $q) => $q->where('branch_id', $user->branch_id));
        }
    }

    /**
     * @param  Builder<PhysicalAssessment>  $query
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function (Builder $q, string $search) {
            $q->whereHas('student', fn (Builder $sq) => $sq->where('name', 'like', "%{$search}%"));
        });
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
