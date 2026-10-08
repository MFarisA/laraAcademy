<?php

namespace App\Models\Assessment\Exam;

use App\Models\Academic\Classroom;
use Database\Factories\Assessment\Exam\ExamSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    protected function token(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value ? Str::upper($value) : null,
        );
    }

    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::upper(Str::random(6));
        } while (static::where('token', $token)->exists());

        return $token;
    }

    /**
     * @return BelongsTo<ExamTemplate, $this>
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ExamTemplate::class, 'exam_template_id');
    }

    /**
     * @return BelongsTo<Classroom, $this>
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    /**
     * @return HasMany<ExamAttempt, $this>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
