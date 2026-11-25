<?php

use App\Http\Controllers\AdminLearnerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ClassroomLessonController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => Auth::check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kelas', [ClassroomController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/semua', [ClassroomController::class, 'catalog'])->name('kelas.catalog');
    Route::get('/kelas/buat', [ClassroomController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [ClassroomController::class, 'store'])->name('kelas.store');
    Route::post('/kelas/{classroom}/enroll', [ClassroomController::class, 'enroll'])->name('kelas.enroll');
    Route::delete('/kelas/{classroom}/enroll', [ClassroomController::class, 'unenroll'])->name('kelas.unenroll');
    Route::get('/kelas/{classroom}/pertemuan/buat', [ClassroomLessonController::class, 'create'])->name('kelas.pertemuan.create');
    Route::post('/kelas/{classroom}/pertemuan', [ClassroomLessonController::class, 'store'])->name('kelas.pertemuan.store');
    Route::get('/kelas/{classroom}/pertemuan/{lesson}/edit', [ClassroomLessonController::class, 'edit'])->name('kelas.pertemuan.edit');
    Route::put('/kelas/{classroom}/pertemuan/{lesson}', [ClassroomLessonController::class, 'update'])->name('kelas.pertemuan.update');
    Route::delete('/kelas/{classroom}/pertemuan/{lesson}', [ClassroomLessonController::class, 'destroy'])->name('kelas.pertemuan.destroy');
    Route::get('/kelas/{classroom}/pertemuan/{lesson}/lampiran', [ClassroomLessonController::class, 'downloadAttachment'])->name('kelas.pertemuan.attachment');
    Route::post('/kelas/{classroom}/pertemuan/{lesson}/selesai', [ClassroomLessonController::class, 'complete'])->name('kelas.pertemuan.complete');
    Route::delete('/kelas/{classroom}/pertemuan/{lesson}/selesai', [ClassroomLessonController::class, 'uncomplete'])->name('kelas.pertemuan.uncomplete');
    Route::post('/kelas/{classroom}/pertemuan/{lesson}/kuis', [ClassroomLessonController::class, 'submitQuiz'])->name('kelas.pertemuan.quiz');
    Route::post('/kelas/{classroom}/pertemuan/{lesson}/pertanyaan', [ClassroomLessonController::class, 'storeQuestion'])->name('kelas.pertemuan.questions.store');
    Route::put('/kelas/{classroom}/pertemuan/{lesson}/pertanyaan/{question}', [ClassroomLessonController::class, 'updateQuestion'])->name('kelas.pertemuan.questions.update');
    Route::delete('/kelas/{classroom}/pertemuan/{lesson}/pertanyaan/{question}', [ClassroomLessonController::class, 'destroyQuestion'])->name('kelas.pertemuan.questions.destroy');
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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
