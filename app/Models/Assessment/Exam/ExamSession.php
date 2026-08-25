<?php

namespace App\Models\Assessment\Exam;

use App\Models\Academic\Classroom;
use Database\Factories\Assessment\Exam\ExamSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'exam_template_id',
    'classroom_id',
    'title',
    'token',
    'start_time',
    'end_time',
])]
class ExamSession extends Model
{
    /** @use HasFactory<ExamSessionFactory> */
    use HasFactory;

    #[\Override]
    public function casts()
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ExamTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ExamTemplate::class);
    }

    /**
     * @return BelongsTo<Classroom, $this>
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * @return HasMany<ExamAttempt, $this>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
