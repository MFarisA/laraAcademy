<?php

namespace App\Models\Assessment\Exam;

use App\Models\Academic\Subject;
use App\Models\Assessment\Question\Question;
use Database\Factories\Assessment\Exam\ExamSectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'exam_template_id',
    'subject_id',
    'title',
    'passing_grade',
    'duration_minutes',
    'order_index',
])]
class ExamSection extends Model
{
    /** @use HasFactory<ExamSectionFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<ExamTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ExamTemplate::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsToMany<Question, $this>
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_section_questions')
            ->using(ExamSectionQuestion::class)
            ->withPivot('order_index')
            ->orderByPivot('order_index');
    }
}
