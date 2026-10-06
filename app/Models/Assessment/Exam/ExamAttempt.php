<?php

namespace App\Models\Assessment\Exam;

use App\Models\User;
use Database\Factories\Assessment\Exam\ExamAttemptFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

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
        return [
            'started_at' => 'datetime',
            'total_score' => 'decimal:2',
            'is_passed' => 'boolean',
            'submitted_at' => 'datetime',
        ];
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
