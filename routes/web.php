<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WeekController as AdminWeekController;
use App\Http\Controllers\Admin\WeekQuizController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\WeekController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\SubmissionAnswerController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\SubmissionController as TeacherSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student', DashboardController::class)->name('student.dashboard');
    Route::get('/student/weeks/{week}', [WeekController::class, 'show'])->name('student.weeks.show');
    Route::get('/student/quizzes/{quiz}', [StudentQuizController::class, 'show'])->name('student.quizzes.show');
    Route::post('/student/quizzes/{quiz}/new-try', [StudentQuizController::class, 'startNewTry'])->name('student.quizzes.new-try');
    Route::get('/student/quizzes/{quiz}/play', [StudentQuizController::class, 'play'])->name('student.quizzes.play');
    Route::post('/student/submissions/{submission}/questions/{question}/audio', [SubmissionAnswerController::class, 'uploadAudio'])->name('student.submissions.audio');
    Route::post('/student/submissions/{submission}/questions/{question}/selection', [SubmissionAnswerController::class, 'saveSelection'])->name('student.submissions.selection');
    Route::post('/student/submissions/{submission}/complete', [SubmissionAnswerController::class, 'complete'])->name('student.submissions.complete');
});

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher', TeacherDashboardController::class)->name('teacher.dashboard');
    Route::get('/teacher/submissions', [TeacherSubmissionController::class, 'index'])->name('teacher.submissions.index');
    Route::get('/teacher/submissions/by-student', [TeacherSubmissionController::class, 'byStudent'])->name('teacher.submissions.by-student');
    Route::get('/teacher/submissions/{submission}', [TeacherSubmissionController::class, 'show'])->name('teacher.submissions.show');
    Route::put('/teacher/submissions/{submission}', [TeacherSubmissionController::class, 'update'])->name('teacher.submissions.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');
    Route::resource('admin/users', UserController::class)->except(['show'])->names('admin.users');
    Route::resource('admin/weeks', AdminWeekController::class)->except(['show'])->names('admin.weeks');
    Route::post('admin/weeks/{week}/quizzes', [WeekQuizController::class, 'store'])->name('admin.weeks.quizzes.store');
    Route::put('admin/weeks/{week}/quizzes/reorder', [WeekQuizController::class, 'reorder'])->name('admin.weeks.quizzes.reorder');
    Route::delete('admin/weeks/{week}/quizzes/{quiz}', [WeekQuizController::class, 'destroy'])->name('admin.weeks.quizzes.destroy');
    Route::resource('manage/quizzes', AdminQuizController::class)->except(['show'])->names('manage.quizzes');
});
