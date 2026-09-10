<?php

use App\Enum\Access\Permission\UserPermissionEnum;
use App\Http\Controllers\Academic\ProgramController;
use App\Http\Controllers\Academic\SubjectController;
use App\Http\Controllers\Account\User\UserController;
use App\Http\Controllers\Organization\Branch\BranchController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:super-admin'])->group(function () {
    Route::resource('branches', BranchController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('subjects', SubjectController::class);
});

Route::middleware(['auth', 'verified', 'permission:' . UserPermissionEnum::VIEW->value])->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__ . '/settings.php';
