<?php

namespace App\Models\Learning;

use App\Enum\Learning\AttendanceStatusEnum;
use App\Models\User;
use Database\Factories\Learning\AttendanceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property Carbon|null $verified_at
 * @property Carbon|null $created_at
 */
#[Fillable([
    'class_schedule_id',
    'student_id',
    'status',
    'verified_at',
])]
class Attendance extends Model
{
    /** @use HasFactory<AttendanceFactory> */
    use HasFactory;

    #[Override]
    public function casts()
    {
        return [
            'status' => AttendanceStatusEnum::class,
            'verified_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ClassSchedule, $this>
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
