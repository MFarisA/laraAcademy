<?php

namespace App\Models\Academic;

use App\Models\Assessment\Exam\ExamSection;
use App\Models\Assessment\Question\Question;
use App\Models\Learning\ClassSchedule;
use Database\Factories\Academic\SubjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'code',
    'description',
])]
class Subject extends Model
{
    /** @use HasFactory<SubjectFactory> */
    use HasFactory;

    /**
     * @return BelongsToMany<Program, $this, ProgramSubject>
     */
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class)
            ->using(ProgramSubject::class)
            ->withPivot('min_passing_score');
    }

    /**
     * @return HasMany<ClassSchedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * @return HasMany<Question, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * @return HasMany<ExamSection, $this>
     */
    public function examSections(): HasMany
    {
        return $this->hasMany(ExamSection::class);
    }
}
