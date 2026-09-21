<?php

namespace App\Models\Academic\Enrollment;

use App\Models\Academic\Classroom;
use App\Models\User;
use Database\Factories\Academic\Enrollment\ClassroomEnrollmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Override;

#[Fillable([
    'classroom_id',
    'student_id',
    'status',
    'enrolled_at',
])]
class ClassroomEnrollment extends Pivot
{
    /** @use HasFactory<ClassroomEnrollmentFactory> */
    use HasFactory;

    protected $table = 'classroom_enrollments';

    #[Override]
    public function casts()
    {
        return [
            'enrolled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Classroom, $this>
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
