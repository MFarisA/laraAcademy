<?php

namespace App\Models\Assessment\Question;

use App\Enum\Assessment\GradingRuleEnum;
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
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use HasFactory, SoftDeletes;

    #[\Override]
    /**
     * @return array{grading_rule: GradingRuleEnum}
     */
    public function casts(): array
    {
        return [
            'grading_rule' => GradingRuleEnum::class,
        ];
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return HasMany<QuestionOption, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    /**
     * @return BelongsToMany<ExamSection, $this, ExamSectionQuestion>
     */
    public function examSections(): BelongsToMany
    {
        return $this->belongsToMany(
            ExamSection::class,
            'exam_section_questions',
        )->using(ExamSectionQuestion::class)->withPivot('order_index');
    }
}
