<?php

namespace App\Models\Academic;

use Database\Factories\Academic\ProgramSubjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable([
    'program_id',
    'subject_id',
    'min_passing_score',
])]
class ProgramSubject extends Pivot
{
    /** @use HasFactory<ProgramSubjectFactory> */
    use HasFactory;

    protected $table = 'program_subjects';

    /**
     * @return BelongsTo<Program, $this>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
