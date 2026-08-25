# Referensi Model & Relasi

Ditulis berdasarkan isi `database/migrations/`. Gunakan sebagai pegangan agar relasi tidak salah tulis.

## Aturan Cepat (agar tidak kelupaan)

| Kondisi di DB | Relasi di Model | Nama Method |
|---|---|---|
| FK ada **di tabel ini** (misal `classrooms.batch_id`) | `belongsTo(ModelLawan::class)` | singular (`batch()`) |
| FK menunjuk ke tabel model ini (misal `branches.id` → `users.branch_id`) | `hasMany(ModelAnak::class)` | jamak (`users()`) |
| Ada **tabel pivot** (misal `program_subjects`) | `belongsToMany(ModelLawan::class)` | jamak (`subjects()`) |
| Tabel pivot punya kolom tambahan | tambahkan `->withPivot('kolom')` | — |
| Pivot pakai `$table->timestamps()` | tambahkan `->withTimestamps()` — **jika tidak ada, JANGAN dipakai** | — |
| Pivot punya class model sendiri | extends `Pivot`, lalu `->using(PivotClass::class)` | — |

**Kesalahan yang sering terjadi (pernah terjadi di repo ini):**
- ❌ Method bernama `programs()` tapi isinya `belongsToMany(Program::class)` → self-referencing. Di `Program` harusnya `subjects()` → `Subject::class`.
- ❌ `withTimestamps()` padahal pivot tidak punya kolom timestamp.
- ❌ Relasi one-to-many ditulis `belongsTo` di sisi induk (yang benar `hasMany`).
- ❌ Menunjuk ke class diri sendiri, misal di `Classroom`: `belongsTo(Classroom::class)` untuk kolom `batch_id` (harusnya `Batch::class`).

---

## Peta Relasi

```
Branch 1─* User
Branch 1─* Classroom *─1 Batch *─1 Program
Program *─* Subject          (pivot: program_subjects, + min_passing_score)
Classroom 1─* ClassSchedule *─1 Subject
User(student) *─* Classroom  (pivot: classroom_enrollments)
Program 1─* ExamTemplate 1─* ExamSection *─* Question (pivot: exam_section_questions)
ExamTemplate 1─* ExamSession 1─* ExamAttempt 1─* ExamAnswer
User(student) 1─* PhysicalAssessment 1─* PhysicalTestScore
```

---

## App\Models\User

```php
<?php

namespace App\Models;

use App\Models\Organization\Branch;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(\App\Models\Academic\Enrollment\ClassroomEnrollment::class, 'student_id');
    }

    public function schedulesAsInstructor(): HasMany
    {
        return $this->hasMany(\App\Models\Learning\ClassSchedule::class, 'instructor_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(\App\Models\Learning\Attendance::class, 'student_id');
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(\App\Models\Assessment\Exam\ExamAttempt::class, 'student_id');
    }

    public function physicalAssessments(): HasMany
    {
        return $this->hasMany(\App\Models\Assessment\Physical\PhysicalAssessment::class, 'student_id');
    }
}
```

> Catatan: `branch_id` nullable di DB, jadi `$user->branch` bisa `null`.

---

## App\Models\Organization\Branch

```php
<?php

namespace App\Models\Organization;

use App\Models\Academic\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'city', 'address'])]
class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\Organization\BranchFactory> */
    use HasFactory;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
```

---

## App\Models\Academic\Program

```php
<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'description'])]
class Program extends Model
{
    /** @use HasFactory<\Database\Factories\Academic\ProgramFactory> */
    use HasFactory;

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)
            ->withPivot('min_passing_score');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function examTemplates(): HasMany
    {
        return $this->hasMany(\App\Models\Assessment\Exam\ExamTemplate::class);
    }

    public function learningMaterials(): HasMany
    {
        return $this->hasMany(\App\Models\Learning\LearningMaterial::class);
    }
}
```

---

## App\Models\Academic\Subject

```php
<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'description'])]
class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\Academic\SubjectFactory> */
    use HasFactory;

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class)
            ->withPivot('min_passing_score');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(\App\Models\Learning\ClassSchedule::class);
    }

    public function learningMaterials(): HasMany
    {
        return $this->hasMany(\App\Models\Learning\LearningMaterial::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(\App\Models\Assessment\Question\Question::class);
    }

    public function examSections(): HasMany
    {
        return $this->hasMany(\App\Models\Assessment\Exam\ExamSection::class);
    }
}
```

---

## App\Models\Academic\ProgramSubject (pivot)

Tabel `program_subjects` tanpa timestamps. Kalau tidak butuh logika bisnis di pivot, **hapus file ini** dan cukup pakai `belongsToMany` dari Program/Subject. Kalau ingin dipertahankan:

```php
<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['program_id', 'subject_id', 'min_passing_score'])]
class ProgramSubject extends Pivot
{
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
```

Lalu di Program/Subject tambahkan `->using(ProgramSubject::class)`. **Jangan extends `Model`** untuk pivot.

---

## App\Models\Academic\Batch

```php
<?php

namespace App\Models\Academic;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['program_id', 'name', 'start_date', 'end_date'])]
class Batch extends Model
{
    /** @use HasFactory<\Database\Factories\Academic\BatchFactory> */
    use HasFactory;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
```

> ⚠️ Sebelumnya `classrooms()` di sini tertulis `belongsTo` — sudah dikoreksi menjadi `hasMany`.

---

## App\Models\Academic\Classroom

```php
<?php

namespace App\Models\Academic;

use App\Models\Organization\Branch;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['batch_id', 'branch_id', 'name', 'capacity'])]
class Classroom extends Model
{
    /** @use HasFactory<\Database\Factories\Academic\ClassroomFactory> */
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
            ->withPivot('status', 'enrolled_at')
            ->as('enrollment');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(\App\Models\Learning\ClassSchedule::class);
    }

    public function examSessions(): HasMany
    {
        return $this->hasMany(\App\Models\Assessment\Exam\ExamSession::class);
    }
}
```

> ⚠️ Sebelumnya `batch()` tertulis `belongsTo(Classroom::class)` — sudah dikoreksi menjadi `Batch::class`.

---

## App\Models\Academic\Enrollment\ClassroomEnrollment (pivot)

Tabel `classroom_enrollments` tanpa auto-increment id dan tanpa timestamps.

```php
<?php

namespace App\Models\Academic\Enrollment;

use App\Models\Academic\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['classroom_id', 'student_id', 'status', 'enrolled_at'])]
class ClassroomEnrollment extends Pivot
{
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
```

Dari sisi User juga bisa:

```php
public function classrooms(): BelongsToMany
{
    return $this->belongsToMany(Classroom::class, 'classroom_enrollments', 'student_id', 'classroom_id')
        ->withPivot('status', 'enrolled_at')
        ->as('enrollment');
}
```

---

## App\Models\Learning\ClassSchedule

```php
<?php

namespace App\Models\Learning;

use App\Models\Academic\Classroom;
use App\Models\Academic\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    /** @use HasFactory<\Database\Factories\Learning\ClassScheduleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
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
```

---

## App\Models\Learning\Attendance

```php
<?php

namespace App\Models\Learning;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['class_schedule_id', 'student_id', 'status', 'verified_at'])]
class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\Learning\AttendanceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
```

---

## App\Models\Learning\LearningMaterial

```php
<?php

namespace App\Models\Learning;

use App\Models\Academic\Program;
use App\Models\Academic\Subject;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subject_id', 'program_id', 'title', 'type', 'file_url', 'is_downloadable'])]
class LearningMaterial extends Model
{
    /** @use HasFactory<\Database\Factories\Learning\LearningMaterialFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_downloadable' => 'boolean',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class); // nullable di DB
    }
}
```

---

## App\Models\Assessment\Question\Question

```php
<?php

namespace App\Models\Assessment\Question;

use App\Models\Academic\Subject;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['subject_id', 'question_text', 'image_url', 'grading_rule', 'difficulty_level'])]
class Question extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Question\QuestionFactory> */
    use HasFactory;

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function examSections(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Assessment\Exam\ExamSection::class,
            'exam_section_questions',
        )->withPivot('order_index');
    }
}
```

---

## App\Models\Assessment\Question\QuestionOption

```php
<?php

namespace App\Models\Assessment\Question;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['question_id', 'option_label', 'option_text', 'is_correct', 'weight_score'])]
class QuestionOption extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Question\QuestionOptionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'weight_score' => 'decimal:2',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
```

---

## App\Models\Assessment\Exam\ExamTemplate

```php
<?php

namespace App\Models\Assessment\Exam;

use App\Models\Academic\Program;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['program_id', 'title', 'type', 'total_duration_minutes'])]
class ExamTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Exam\ExamTemplateFactory> */
    use HasFactory;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ExamSection::class)->orderBy('order_index');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}
```

---

## App\Models\Assessment\Exam\ExamSection

```php
<?php

namespace App\Models\Assessment\Exam;

use App\Models\Academic\Subject;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['exam_template_id', 'subject_id', 'title', 'passing_grade', 'duration_minutes', 'order_index'])]
class ExamSection extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Exam\ExamSectionFactory> */
    use HasFactory;

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExamTemplate::class, 'exam_template_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Assessment\Question\Question::class,
            'exam_section_questions',
        )->withPivot('order_index')->orderByPivot('order_index');
    }
}
```

---

## App\Models\Assessment\Exam\ExamSectionQuestion (pivot)

Tabel `exam_section_questions` tanpa timestamps. Sama seperti `ProgramSubject` — hapus jika tidak perlu logika bisnis, atau ubah ke `Pivot`:

```php
<?php

namespace App\Models\Assessment\Exam;

use App\Models\Assessment\Question\Question;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['exam_section_id', 'question_id', 'order_index'])]
class ExamSectionQuestion extends Pivot
{
    public function section(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class, 'exam_section_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
```

Tambahkan `->using(ExamSectionQuestion::class)` pada kedua sisi `belongsToMany` di atas.

---

## App\Models\Assessment\Exam\ExamSession

```php
<?php

namespace App\Models\Assessment\Exam;

use App\Models\Academic\Classroom;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['exam_template_id', 'classroom_id', 'title', 'token', 'start_time', 'end_time'])]
class ExamSession extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Exam\ExamSessionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ExamTemplate::class, 'exam_template_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class); // nullable di DB
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
```

---

## App\Models\Assessment\Exam\ExamAttempt

```php
<?php

namespace App\Models\Assessment\Exam;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'exam_session_id',
    'student_id',
    'status',
    'total_score',
    'national_rank',
    'branch_rank',
    'is_passed',
    'started_at',
    'submitted_at',
])]
class ExamAttempt extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Exam\ExamAttemptFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'is_passed' => 'boolean',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }
}
```

---

## App\Models\Assessment\Exam\ExamAnswer

```php
<?php

namespace App\Models\Assessment\Exam;

use App\Models\Assessment\Question\Question;
use App\Models\Assessment\Question\QuestionOption;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['exam_attempt_id', 'question_id', 'selected_option_id', 'score_earned', 'time_spent_seconds'])]
class ExamAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Exam\ExamAnswerFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'score_earned' => 'decimal:2',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id'); // nullable di DB
    }
}
```

---

## App\Models\Assessment\Physical\PhysicalAssessment

```php
<?php

namespace App\Models\Assessment\Physical;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['student_id', 'evaluator_id', 'assessment_date', 'total_physical_score', 'notes'])]
class PhysicalAssessment extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Physical\PhysicalAssessmentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'total_physical_score' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id'); // nullable di DB
    }

    public function testScores(): HasMany
    {
        return $this->hasMany(PhysicalTestScore::class);
    }
}
```

---

## App\Models\Assessment\Physical\PhysicalTestScore

```php
<?php

namespace App\Models\Assessment\Physical;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['physical_assessment_id', 'metric_name', 'raw_value', 'calculated_score'])]
class PhysicalTestScore extends Model
{
    /** @use HasFactory<\Database\Factories\Assessment\Physical\PhysicalTestScoreFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'calculated_score' => 'decimal:2',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(PhysicalAssessment::class, 'physical_assessment_id');
    }
}
```
