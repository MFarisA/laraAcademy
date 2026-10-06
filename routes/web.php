<?php

use App\Enum\Access\Permission\UserPermissionEnum;
use App\Http\Controllers\Academic\BatchController;
use App\Http\Controllers\Academic\ClassroomController;
use App\Http\Controllers\Academic\ClassroomEnrollmentController;
use App\Http\Controllers\Academic\ProgramController;
use App\Http\Controllers\Academic\ProgramSubjectController;
use App\Http\Controllers\Academic\SubjectController;
use App\Http\Controllers\Account\User\UserController;
use App\Http\Controllers\Assessment\CbtController;
use App\Http\Controllers\Assessment\ExamSessionController;
use App\Http\Controllers\Assessment\ExamTemplateController;
use App\Http\Controllers\Assessment\QuestionController;
use App\Http\Controllers\Learning\AttendanceController;
use App\Http\Controllers\Learning\ClassScheduleController;
use App\Http\Controllers\Learning\LearningMaterialController;
use App\Http\Controllers\Organization\Branch\BranchController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::get('materials/{material}/stream', [LearningMaterialController::class, 'stream'])
        ->name('materials.stream')
        ->middleware('signed');
});

Route::middleware(['auth', 'verified', 'role:super-admin|admin-branch'])->group(function () {
    Route::inertia('admin/dashboard', 'dashboard')->name('admin.dashboard');
});

Route::middleware(['auth', 'verified', 'role:instructor|super-admin'])->group(function () {
    Route::inertia('schedules', 'dashboard')->name('schedules.index');
    Route::post('materials', [LearningMaterialController::class, 'store'])->name('materials.store');
    Route::get('subjects/{subject}/materials', [LearningMaterialController::class, 'index'])->name('subjects.materials.index');
});

Route::middleware(['auth', 'verified', 'role:super-admin'])->group(function () {
    Route::resource('branches', BranchController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('batches', BatchController::class);
    Route::resource('classrooms', ClassroomController::class);
    Route::resource('schedules', ClassScheduleController::class);
    Route::resource('questions', QuestionController::class);

    // Exam Management (Authoring & Sessions)
    Route::resource('exam-templates', ExamTemplateController::class);
    Route::resource('exam-sessions', ExamSessionController::class);
    Route::post('exam-sessions/{exam_session}/regenerate-token', [ExamSessionController::class, 'regenerateToken'])
        ->name('exam-sessions.regenerate-token');

    Route::put('programs/{programs}/subjects', [ProgramSubjectController::class, 'update'])->name('programs.subjects.update');

    Route::post('classrooms/{classroom}/enrollments', [ClassroomEnrollmentController::class, 'store'])->name('classrooms.enrollments.store');
    Route::patch('classrooms/{classroom}/enrollments/{student}', [ClassroomEnrollmentController::class, 'update'])->name('classrooms.enrollments.update');
    Route::delete('classrooms/{classroom}/enrollments/{student}', [ClassroomEnrollmentController::class, 'destroy'])->name('classrooms.enrollments.destroy');

    Route::get('classrooms/{classroom}/attendance-summary', [AttendanceController::class, 'attendanceSummary'])->name('classrooms.attendance.summary');
    Route::post('schedules/{schedule}/attendances', [AttendanceController::class, 'recordAttendance'])->name('schedules.attendances.record');
});

// Student CBT Portal
Route::middleware(['auth', 'verified', 'role:student|super-admin'])->prefix('cbt')->name('cbt.')->group(function () {
    Route::post('sessions/{exam_session}/enter', [CbtController::class, 'enter'])->name('sessions.enter');
    Route::match(['get', 'post'], 'sessions/{exam_session}/start', [CbtController::class, 'start'])->name('sessions.start');
    Route::put('attempts/{attempt}/answers', [CbtController::class, 'saveAnswer'])->name('attempts.answers.save');
    Route::post('attempts/{attempt}/submit', [CbtController::class, 'submit'])->name('attempts.submit');
});

Route::middleware(['auth', 'verified', 'permission:'.UserPermissionEnum::VIEW->value, 'branch.context'])->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__.'/settings.php';
