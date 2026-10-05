<?php

namespace App\Models\Assessment\Question;

use App\Enum\Assessment\DifficultyLevelEnum;
use App\Enum\Assessment\GradingRuleEnum;
use App\Models\Academic\Subject;
use App\Models\Assessment\Exam\ExamSection;
use App\Models\Assessment\Exam\ExamSectionQuestion;
use Database\Factories\Assessment\Question\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
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
/**
 * @property GradingRuleEnum $grading_rule
 */
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @return array<string, string>
     */
    #[\Override]
    public function casts(): array
    {
        return [
            'grading_rule' => GradingRuleEnum::class,
            'difficulty_level' => DifficultyLevelEnum::class,
        ];
    }

    /**
     * @param  Builder<Question>  $query
     * @return Builder<Question>
     */
    public function scopeSearchText(Builder $query, string $term): Builder
    {
        $cleanTerm = trim($term);

        return $query
            ->whereRaw("to_tsvector('indonesian', question_text) @@websearch_to_tsquery('indonesian', ?)", [$cleanTerm])
            ->orderByRaw("ts_rank(to_tsvector('indonesian', question_text), websearch_to_tsquery('indonesian', ?)) DESC", [$cleanTerm])
            ->orderBy('id');
    }

    /**
     * Scope filter kriteria dan pencarian.
     *
     * @param  Builder<static>  $query
     * @param  array<string, mixed>  $filter
     * @return Builder<static>
     */
    public function scopeFilter(Builder $query, array $filter): Builder
    {
        $query->when(
            filled($filter['search'] ?? null),
            fn(Builder $builder) => $this->scopeSearchText($builder, (string) $filter['search']),
            fn(Builder $builder) => $builder->latest('id'),
        );
        $query->when(
            filled($filter['subject_id'] ?? null),
            fn(Builder $builder) => $builder->where('subject_id', $filter['subject_id']),
        );
        $query->when(
            filled($filter['difficulty_level'] ?? null),
            fn(Builder $builder) => $builder->where(
                'difficulty_level',
                $filter['difficulty_level'] instanceof DifficultyLevelEnum
                    ? $filter['difficulty_level']->value
                    : $filter['difficulty_level']
            )
        );
        $query->when(
            filled($filter['grading_rule'] ?? null),
            fn(Builder $builder) => $builder->where(
                'grading_rule',
                $filter['grading_rule'] instanceof GradingRuleEnum
                    ? $filter['grading_rule']->value
                    : $filter['grading_rule']
            )
        );

        return $query;
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
