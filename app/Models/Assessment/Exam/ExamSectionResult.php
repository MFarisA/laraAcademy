<?php

namespace App\Models\Assessment\Exam;

use Database\Factories\Assessment\Exam\ExamSectionResultFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'exam_attempt_id',
    'exam_section_id',
    'score',
    'is_passed',
])]
class ExamSectionResult extends Model
{
    /** @use HasFactory<ExamSectionResultFactory> */
    use HasFactory;

    #[Override]
    public function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_passed' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<ExamAttempt, $this>
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    /**
     * @return BelongsTo<ExamSection, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class, 'exam_section_id');
    }
}
