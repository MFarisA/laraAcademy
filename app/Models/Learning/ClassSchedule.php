<?php

namespace App\Models\Learning;

use App\Models\Academic\Classroom;
use App\Models\Academic\Subject;
use App\Models\User;
use Database\Factories\Learning\ClassScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable([
    'classroom_id',
    'subject_id',
    'instructor_id',
    'room_type',
    'meeting_link',
    'scheduled_at',
    'duration_minutes',
])]
class ClassSchedule extends Model
{
    /** @use HasFactory<ClassScheduleFactory> */
    use HasFactory;

    #[Override]
    public function casts()
    {
        return [
            'scheduled_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
