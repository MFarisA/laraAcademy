<?php

namespace App\Models\Assessment\Exam;

use App\Models\Assessment\Question\Question;
use Database\Factories\Assessment\Exam\ExamSectionQuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable([
    'exam_section_id',
    'question_id',
    'order_index',
])]
class ExamSectionQuestion extends Pivot
{
    /** @use HasFactory<ExamSectionQuestionFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<ExamSection, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class);
    }

    /**
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
