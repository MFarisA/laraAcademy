<?php

namespace App\Actions\Action\Academic\Enrollment;

use App\Enum\Academic\EnrollmentStatusEnum;
use App\Models\Academic\Classroom;
use App\Models\User;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateEnrollmentStatusAction
{
    use AsAction;

    public function handle(Classroom $classroom, User $student, EnrollmentStatusEnum $status): void
    {
        $classroom->students()->updateExistingPivot($student->id, [
            'status' => $status->value
        ]);
    }
}
