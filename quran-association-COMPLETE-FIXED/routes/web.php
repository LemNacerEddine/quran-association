<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CircleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\GuardianAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// إدارة الحلقات (مؤقتاً بدون حماية للاختبار)
Route::resource('circles', CircleController::class);

Route::middleware(['web'])->group(function () {    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard Routes (مؤقتاً بدون حماية للاختبار)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
// إدارة الطلاب
Route::resource('students', StudentController::class);

// إدارة المعلمين
Route::resource('teachers', TeacherController::class);

// إدارة الجدولة (مؤقتاً بدون حماية للاختبار)
Route::resource('schedules', ScheduleController::class);
Route::prefix('schedules')->name('schedules.')->group(function () {
    Route::get('/weekly', [ScheduleController::class, 'weekly'])->name('weekly');
    Route::post('/toggle-status/{schedule}', [ScheduleController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/monthly', [ScheduleController::class, 'monthly'])->name('monthly');
    Route::get('/check-conflicts', [ScheduleController::class, 'checkConflicts'])->name('check-conflicts');
    Route::get('/available-rooms', [ScheduleController::class, 'getAvailableRooms'])->name('available-rooms');
    Route::get('/get-circle-info/{circle}', [ScheduleController::class, 'getCircleInfo'])->name('get-circle-info');
    Route::post('/bulk-action', [ScheduleController::class, 'bulkAction'])->name('bulk-action');
    // New simplified session creation routes
    Route::post('/{schedule}/create-sessions-auto', [ScheduleController::class, 'createSessionsAuto'])->name('create-sessions-auto');
    Route::post('/{schedule}/recreate-sessions', [ScheduleController::class, 'recreateSessions'])->name('recreate-sessions');
    // Legacy routes (kept for compatibility)
    Route::post('/create-weekly-sessions', [ScheduleController::class, 'createWeeklySessions'])->name('create-weekly-sessions');
    Route::post('/create-monthly-sessions', [ScheduleController::class, 'createMonthlySessions'])->name('create-monthly-sessions');
    Route::post('/{schedule}/create-sessions', [ScheduleController::class, 'createSessions'])->name('create-sessions');
});

Route::middleware(['web'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// إدارة الجلسات (مؤقتاً بدون حماية للاختبار)
Route::resource('sessions', SessionController::class);
Route::prefix('sessions')->name('sessions.')->group(function () {
    Route::get('/quick-attendance', [SessionController::class, 'quickAttendanceForm'])->name('quick-attendance');
    Route::post('/quick-attendance', [SessionController::class, 'storeQuickAttendance'])->name('quick-attendance.store');
    Route::get('/quick-attendance/{session}', [SessionController::class, 'quickAttendance'])->name('quick-attendance.session');
    Route::post('/quick-attendance/{session}', [SessionController::class, 'storeQuickAttendanceSession'])->name('quick-attendance.session.store');
    Route::get('/weekly', [SessionController::class, 'weekly'])->name('weekly');
    Route::get('/monthly', [SessionController::class, 'monthly'])->name('monthly');
    Route::get('/calendar', [SessionController::class, 'calendar'])->name('calendar');
});

// إدارة الحضور (مؤقتاً بدون حماية للاختبار)
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('/create', [AttendanceController::class, 'create'])->name('create');
    Route::post('/', [AttendanceController::class, 'store'])->name('store');
    Route::get('/session/{session}', [AttendanceController::class, 'showSession'])->name('session');
    Route::post('/session/{session}', [AttendanceController::class, 'storeSession'])->name('session.store');
    Route::post('/storeSession/{session}', [AttendanceController::class, 'storeSession'])->name('storeSession');
    Route::get('/student/{student}', [AttendanceController::class, 'studentHistory'])->name('student');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // التقارير
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/circles', [ReportController::class, 'circles'])->name('circles');
        Route::get('/students', [ReportController::class, 'students'])->name('students');
        Route::get('/teachers', [ReportController::class, 'teachers'])->name('teachers');
    });
});

require __DIR__.'/auth.php';



// Guardian Management Routes (Admin) - مؤقتاً بدون حماية للاختبار
// Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('guardians', GuardianController::class);
    Route::post('guardians/{guardian}/toggle-status', [GuardianController::class, 'toggleStatus'])->name('guardians.toggleStatus');
    Route::post('guardians/{guardian}/reset-access-code', [GuardianController::class, 'resetAccessCode'])->name('guardians.resetAccessCode');
    Route::get('guardians-search', [GuardianController::class, 'search'])->name('guardians.search');
// });

// Guardian Authentication Routes
Route::prefix('guardian')->name('guardian.')->group(function () {
    // Guest routes (not authenticated) - مؤقتاً بدون middleware
    // Route::middleware('guest:guardian')->group(function () {
        Route::get('login', [GuardianAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [GuardianAuthController::class, 'login']);
        Route::get('reset-code', [GuardianAuthController::class, 'showResetCodeForm'])->name('reset-code');
        Route::post('reset-code', [GuardianAuthController::class, 'resetCode']);
    // });
    
    // Authenticated routes - مؤقتاً بدون middleware
    // Route::middleware('auth:guardian')->group(function () {
        Route::post('logout', [GuardianAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [GuardianAuthController::class, 'dashboard'])->name('dashboard');
        Route::get('profile', [GuardianAuthController::class, 'profile'])->name('profile');
        Route::post('profile', [GuardianAuthController::class, 'updateProfile'])->name('profile.update');
        Route::get('student/{student}', [GuardianAuthController::class, 'showStudent'])->name('student');
        Route::get('student/{student}/reports', [GuardianAuthController::class, 'studentReports'])->name('student.reports');
    // });
});


// Teacher Authentication Routes
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/login', [App\Http\Controllers\TeacherAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\TeacherAuthController::class, 'login']);
    Route::get('/logout', [App\Http\Controllers\TeacherAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [App\Http\Controllers\TeacherAuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\TeacherAuthController::class, 'profile'])->name('profile');
    
    // Teacher Session Management
    Route::prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [App\Http\Controllers\TeacherSessionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TeacherSessionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TeacherSessionController::class, 'store'])->name('store');
        Route::get('/{session}', [App\Http\Controllers\TeacherSessionController::class, 'show'])->name('show');
        Route::get('/{session}/edit', [App\Http\Controllers\TeacherSessionController::class, 'edit'])->name('edit');
        Route::put('/{session}', [App\Http\Controllers\TeacherSessionController::class, 'update'])->name('update');
        Route::delete('/{session}', [App\Http\Controllers\TeacherSessionController::class, 'destroy'])->name('destroy');
        Route::post('/{session}/start', [App\Http\Controllers\TeacherSessionController::class, 'start'])->name('start');
        Route::get('/{session}/live', [App\Http\Controllers\TeacherSessionController::class, 'live'])->name('live');
        Route::post('/{session}/end', [App\Http\Controllers\TeacherSessionController::class, 'end'])->name('end');
        Route::get('/{session}/attendance', [App\Http\Controllers\TeacherSessionController::class, 'attendance'])->name('attendance');
        Route::post('/{session}/attendance', [App\Http\Controllers\TeacherSessionController::class, 'storeAttendance'])->name('attendance.store');
    });
    
    // Teacher functionality routes (to be implemented)
    Route::get('/circle/{circle}/attendance', function($circle) {
        return view('teacher-auth.attendance', compact('circle'));
    })->name('circle.attendance');
    
    Route::get('/circle/{circle}/students', function($circle) {
        return view('teacher-auth.students', compact('circle'));
    })->name('circle.students');
    
    Route::get('/circle/{circle}/grades', function($circle) {
        return view('teacher-auth.grades', compact('circle'));
    })->name('circle.grades');
    
    Route::get('/attendance/today', function() {
        return view('teacher-auth.attendance-today');
    })->name('attendance.today');
    
    Route::get('/reports', function() {
        return view('teacher-auth.reports');
    })->name('reports');
});

