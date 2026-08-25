<?php

namespace App\Models\Assessment\Question;

use App\Models\Academic\Subject;
use App\Models\Assessment\Exam\ExamSection;
use App\Models\Assessment\Exam\ExamSectionQuestion;
use Database\Factories\Assessment\Question\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'subject_id',
    'question_text',
    'image_url',
    'grading_rule',
    'difficulty_level',
])]
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function examSections(): BelongsToMany
    {
        return $this->belongsToMany(
            ExamSection::class,
            'exam_section_questions',
        )->using(ExamSectionQuestion::class)->withPivot('order_index');
    }
}
