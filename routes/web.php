<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\SessionAssessmentController;
use App\Http\Controllers\AdvisorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SymptomAdvisorController;
use App\Http\Controllers\HealthTrendsController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\ReportsController;

/* -------------------- Guest routes -------------------- */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

/* -------------------- Authenticated routes -------------------- */
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // -------------------- Assessment: category picker + adaptive session --------------------
Route::prefix('assessment')->name('assessment.')->group(function () {
    // Landing page — category picker
    Route::get('/', [SessionAssessmentController::class, 'choose'])->name('index');
    Route::get('/choose', [SessionAssessmentController::class, 'choose'])->name('choose');

    // Start the adaptive session
    Route::post('/start', [SessionAssessmentController::class, 'start'])->name('session.start');

    // Live adaptive session
    Route::get('/run/{session}', [SessionAssessmentController::class, 'run'])->name('session.run');
    Route::get('/{session}/next', [SessionAssessmentController::class, 'next'])->name('session.next');
    Route::post('/{session}/answer', [SessionAssessmentController::class, 'answer'])->name('session.answer');
    Route::post('/{session}/finish', [SessionAssessmentController::class, 'finish'])->name('session.finish');

    // Report
    Route::get('/report/{report}', [SessionAssessmentController::class, 'report'])->name('report.show');

    // Legacy single-shot (video etc.) — keep for backward compatibility
    Route::get('/video', [AssessmentController::class, 'video'])->name('video');
    Route::post('/submit', [AssessmentController::class, 'store'])->name('store');
    Route::get('/{assessment}/results', [AssessmentController::class, 'results'])->name('results');
});

    Route::get('/report/{report}', [SessionAssessmentController::class, 'report'])->name('assessment.report.show');

    Route::prefix('symptom-advisor')->name('symptom.')->group(function () {
        Route::get('/', [SymptomAdvisorController::class, 'index'])->name('index');
        Route::post('/ask', [SymptomAdvisorController::class, 'ask'])->name('ask');
        Route::get('/response/{response}', [SymptomAdvisorController::class, 'response'])->name('response');
    });

    Route::get('/trends', [HealthTrendsController::class, 'index'])->name('trends.index');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    Route::prefix('medications')->name('medications.')->group(function () {
        Route::get('/', [MedicationController::class, 'index'])->name('index');
        Route::get('/create', [MedicationController::class, 'create'])->name('create');
        Route::post('/', [MedicationController::class, 'store'])->name('store');
        Route::post('/{medication}/log', [MedicationController::class, 'log'])->name('log');
        Route::delete('/{medication}', [MedicationController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('can:advisor')->prefix('advisor')->name('advisor.')->group(function () {
        Route::get('/dashboard', [AdvisorController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [AdvisorController::class, 'students'])->name('students');
        Route::get('/students/{student}', [AdvisorController::class, 'studentDetail'])->name('student.detail');
        Route::post('/alerts/{alert}/acknowledge', [AdvisorController::class, 'acknowledgeAlert'])->name('alerts.acknowledge');
        Route::post('/alerts/{alert}/resolve', [AdvisorController::class, 'resolveAlert'])->name('alerts.resolve');
        Route::post('/students/{student}/recommendations', [AdvisorController::class, 'addRecommendation'])->name('recommendations.store');
    });

    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'createUser'])->name('users.store');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/assessments', [AdminController::class, 'assessments'])->name('assessments');
        Route::get('/alerts', [AdminController::class, 'alerts'])->name('alerts');
        Route::get('/tips', [AdminController::class, 'healthTips'])->name('tips');
        Route::post('/tips', [AdminController::class, 'createHealthTip'])->name('tips.store');
        Route::put('/tips/{tip}', [AdminController::class, 'updateHealthTip'])->name('tips.update');
        Route::delete('/tips/{tip}', [AdminController::class, 'deleteHealthTip'])->name('tips.delete');
    });
	// -------------------- Adaptive AI session assessment --------------------
Route::prefix('session')->name('assessment.session.')->group(function () {
    Route::get('/choose', [SessionAssessmentController::class, 'choose'])->name('choose');
    Route::post('/start', [SessionAssessmentController::class, 'start'])->name('start');
    Route::get('/{session}/run', [SessionAssessmentController::class, 'run'])->name('run');
    Route::get('/{session}/next', [SessionAssessmentController::class, 'next'])->name('next');
    Route::post('/{session}/answer', [SessionAssessmentController::class, 'answer'])->name('answer');
    Route::post('/{session}/finish', [SessionAssessmentController::class, 'finish'])->name('finish');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReportsController::class, 'index'])->name('index');
    Route::post('/sentiment', [\App\Http\Controllers\ReportsController::class, 'sentiment'])->name('sentiment');
    Route::get('/export/{format}', [\App\Http\Controllers\ReportsController::class, 'export'])->name('export');
});
});

Route::get('/up', fn () => response()->json(['status' => 'ok']));