<?php

namespace App\Models\Assessment\Exam;

use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use Database\Factories\Assessment\Exam\ExamAnswerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

#[Fillable([
    'exam_attempt_id',
    'question_id',
    'selected_option_id',
    'score_earned',
    'time_spent_seconds',
])]
class ExamAnswer extends Model
{
    /** @use HasFactory<ExamAnswerFactory> */
    use HasFactory;

    #[Override]
    public function casts()
    {
        return [
            'score_earned' => 'decimal:2',
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
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * @return BelongsTo<QuestionOption, $this>
     */
    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
