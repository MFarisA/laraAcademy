<?php

use App\Http\Controllers\Organization\Branch\BranchController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:super-admin'])->group(function () {
    Route::resource('branches', BranchController::class);
});

require __DIR__ . '/settings.php';
