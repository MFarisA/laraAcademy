<?php

namespace App\Models\Assessment\Exam;

use App\Enum\Assessment\ExamAttemptStatusEnum;
use App\Models\User;
use Database\Factories\Assessment\Exam\ExamAttemptFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property Carbon|null $started_at
 * @property Carbon|null $submitted_at
 * @property ExamAttemptStatusEnum $status
 *
 * @method static Builder<static> leaderboard(string $scope = 'national', ?int $branchId = null)
 */
#[Fillable([
    'exam_session_id',
    'student_id',
    'status',
    'total_score',
    'national_rank',
    'branch_rank',
    'is_passed',
    'started_at',
    'submitted_at',
])]
class ExamAttempt extends Model
{
    /** @use HasFactory<ExamAttemptFactory> */
    use HasFactory;

    #[Override]
    public function casts(): array
    {
        return ['status' => ExamAttemptStatusEnum::class,
            'started_at' => 'datetime',
            'total_score' => 'decimal:2',
            'is_passed' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * Durasi pengerjaan dalam detik.
     *
     * @return Attribute<int|null, never>
     */
    protected function durationSeconds(): Attribute
    {
        return Attribute::make(
            get: function (): ?int {
                if (! $this->started_at || ! $this->submitted_at) {
                    return null;
                }

                return (int) Carbon::parse($this->submitted_at)->diffInSeconds(Carbon::parse($this->started_at));
            }
        );
    }

    /**
     * Scope query untuk leaderboard berdasarkan scope ('national' atau 'branch').
     *
     * @param  Builder<ExamAttempt>  $query
     */
    public function scopeLeaderboard(Builder $query, string $scope = 'national', ?int $branchId = null): void
    {
        $query->where('status', ExamAttemptStatusEnum::COMPLETED);

        if ($scope === 'branch' && $branchId !== null) {
            $query->whereHas('student', fn (Builder $q) => $q->where('branch_id', $branchId))
                ->orderBy('branch_rank', 'asc');
        } else {
            $query->orderBy('national_rank', 'asc');
        }
    }

    /**
     * @return BelongsTo<ExamSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * @return HasMany<ExamAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    /**
     * @return HasMany<ExamSectionResult, $this>
     */
    public function sectionResults(): HasMany
    {
        return $this->hasMany(ExamSectionResult::class, 'exam_attempt_id');
    }
}
