<?php

namespace App\Models\Academic;

use Database\Factories\Academic\BatchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'program_id',
    'name',
    'start_date',
    'end_date',
])]
class Batch extends Model
{
    /** @use HasFactory<BatchFactory> */
    use HasFactory;

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
