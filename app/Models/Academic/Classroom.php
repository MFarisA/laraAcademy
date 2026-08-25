<?php

namespace App\Models\Academic;

use App\Models\Academic\Enrollment\ClassroomEnrollment;
use App\Models\Assessment\Exam\ExamSession;
use App\Models\Learning\ClassSchedule;
use App\Models\Organization\Branch;
use App\Models\User;
use Database\Factories\Academic\ClassroomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'batch_id',
    'branch_id',
    'name',
    'capacity',
])]
class Classroom extends Model
{
    /** @use HasFactory<ClassroomFactory> */
    use HasFactory;

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_enrollments', 'classroom_id', 'student_id')
            ->using(ClassroomEnrollment::class)
            ->withPivot('status', 'enrolled_at')
            ->as('enrollment');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}
