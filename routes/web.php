<?php

use App\Enum\Access\Permission\UserPermissionEnum;
use App\Http\Controllers\Academic\BatchController;
use App\Http\Controllers\Academic\ClassroomController;
use App\Http\Controllers\Academic\ProgramController;
use App\Http\Controllers\Academic\SubjectController;
use App\Http\Controllers\Account\User\UserController;
use App\Http\Controllers\Organization\Branch\BranchController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:super-admin|admin-branch'])->group(function () {
    Route::inertia('admin/dashboard', 'dashboard')->name('admin.dashboard');
});

Route::middleware(['auth', 'verified', 'role:instructor|super-admin'])->group(function () {
    Route::inertia('schedules', 'dashboard')->name('schedules.index');
});

Route::middleware(['auth', 'verified', 'role:super-admin'])->group(function () {
    Route::resource('branches', BranchController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('batches', BatchController::class);
    Route::resource('classrooms', ClassroomController::class);
});

Route::middleware(['auth', 'verified', 'permission:' . UserPermissionEnum::VIEW->value, 'branch.context'])->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__ . '/settings.php';
