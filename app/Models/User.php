<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Concerns\BelongsToBranch;
use App\Models\Academic\Enrollment\ClassroomEnrollment;
use App\Models\Assessment\Exam\ExamAttempt;
use App\Models\Assessment\Physical\PhysicalAssessment;
use App\Models\Learning\Attendance;
use App\Models\Learning\ClassSchedule;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> filter(array<string, mixed> $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static> accessibleBy(?\App\Models\User $user = null)
 */
#[Fillable(['name', 'email', 'password', 'is_active', 'branch_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use BelongsToBranch, HasFactory, HasRoles, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @param  Builder<User>  $query
     * @param  array<mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, fn (Builder $q, string $search) => $q
            ->whereAny(['name', 'email'], 'like', "%{$search}%"));

        $query->when(filled($filters['is_active'] ?? null), fn (Builder $q) => $q
            ->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN)));

        $query->when($filters['branch_id'] ?? null, fn (Builder $q, $branchId) => $q
            ->where('branch_id', $branchId));

        $query->when($filters['roles'] ?? null, fn (Builder $q, string $role) => $q
            ->whereRelation('roles', 'name', $role));
    }

    /**
     * @return HasMany<ClassroomEnrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassroomEnrollment::class, 'student_id');
    }

    /**
     * @return HasMany<ClassSchedule, $this>
     */
    public function schedulesAsInstructor(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'instructor_id');
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /**
     * @return HasMany<ExamAttempt, $this>
     */
    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class, 'student_id');
    }

    /**
     * @return HasMany<PhysicalAssessment, $this>
     */
    public function physicalAssessments(): HasMany
    {
        return $this->hasMany(PhysicalAssessment::class, 'student_id');
    }
}
