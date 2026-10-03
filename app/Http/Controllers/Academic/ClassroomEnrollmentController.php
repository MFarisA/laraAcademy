<?php

namespace App\Http\Controllers\Academic;

use App\Actions\Academic\Enrollment\EnrollStudentAction;
use App\Actions\Academic\Enrollment\UpdateEnrollmentStatusAction;
use App\Enum\Academic\EnrollmentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Academic\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\Academic\Enrollment\UpdateEnrollmentRequest;
use App\Models\Academic\Classroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ClassroomEnrollmentController extends Controller
{
    public function store(
        StoreEnrollmentRequest $request,
        Classroom $classroom,
        EnrollStudentAction $action
    ): RedirectResponse {
        /**
         * @var User $student
         */
        $student = User::findOrFail($request->validated('student_id'));
        $action->handle($classroom, $student);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Siswa berhasil didaftarkan ke kelas']);

        return back();
    }

    public function update(
        UpdateEnrollmentRequest $request,
        Classroom $classroom,
        User $student,
        UpdateEnrollmentStatusAction $action
    ): RedirectResponse {
        $status = EnrollmentStatusEnum::from($request->validated('status'));
        $action->handle($classroom, $student, $status);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Status Pendaftaran Siswa berhasil diperbarui.']);

        return back();
    }

    public function destroy(
        Classroom $classroom,
        User $student,
        UpdateEnrollmentStatusAction $action
    ): RedirectResponse {
        // Sesuai aturan: dropout/keluar mengubah status jadi dropped, bukan menghapus baris
        $action->handle($classroom, $student, EnrollmentStatusEnum::DROPPED);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Siswa telah dikeluarkan dari kelas']);

        return back();
    }
}
