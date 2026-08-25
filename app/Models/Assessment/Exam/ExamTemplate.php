<?php

namespace App\Models\Assessment\Exam;

use App\Models\Academic\Program;
use Database\Factories\Assessment\Exam\ExamTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'program_id',
    'title',
    'type',
    'total_duration_minutes',
])]
class ExamTemplate extends Model
{
    /** @use HasFactory<ExamTemplateFactory> */
    use HasFactory;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ExamSection::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}
