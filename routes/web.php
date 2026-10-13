<?php

use App\Http\Controllers\AdminLearnerController;
use App\Http\Controllers\ClassroomController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => Auth::check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kelas', [ClassroomController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/semua', [ClassroomController::class, 'catalog'])->name('kelas.catalog');
    Route::get('/kelas/buat', [ClassroomController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [ClassroomController::class, 'store'])->name('kelas.store');
    Route::post('/kelas/{classroom}/enroll', [ClassroomController::class, 'enroll'])->name('kelas.enroll');
    Route::delete('/kelas/{classroom}/enroll', [ClassroomController::class, 'unenroll'])->name('kelas.unenroll');
    Route::get('/kelas/{classroom}', [ClassroomController::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{classroom}/edit', [ClassroomController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{classroom}', [ClassroomController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{classroom}', [ClassroomController::class, 'destroy'])->name('kelas.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/learners', [AdminLearnerController::class, 'index'])->name('learners.index');
        Route::get('/learners/create', [AdminLearnerController::class, 'create'])->name('learners.create');
        Route::post('/learners', [AdminLearnerController::class, 'store'])->name('learners.store');
        Route::get('/learners/{user}/edit', [AdminLearnerController::class, 'edit'])->name('learners.edit');
        Route::put('/learners/{user}', [AdminLearnerController::class, 'update'])->name('learners.update');
        Route::delete('/learners/{user}', [AdminLearnerController::class, 'destroy'])->name('learners.destroy');
    });

    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
});
