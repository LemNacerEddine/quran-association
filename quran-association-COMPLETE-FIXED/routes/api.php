<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\MemorizationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CircleController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });

    // Parent routes
    Route::prefix('parent')->group(function () {
        Route::get('children', [ParentController::class, 'getChildren']);
        Route::get('children/{student}/progress', [ParentController::class, 'getChildProgress']);
        Route::get('children/{student}/attendance', [ParentController::class, 'getChildAttendance']);
        Route::get('children/{student}/memorization', [ParentController::class, 'getChildMemorization']);
        Route::get('dashboard', [ParentController::class, 'getDashboard']);
    });

    // Student routes (for admin use)
    Route::prefix('students')->middleware('role:admin')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::post('/', [StudentController::class, 'store']);
        Route::get('{student}', [StudentController::class, 'show']);
        Route::put('{student}', [StudentController::class, 'update']);
        Route::delete('{student}', [StudentController::class, 'destroy']);
        Route::get('{student}/progress', [StudentController::class, 'getProgress']);
        Route::get('{student}/attendance', [StudentController::class, 'getAttendance']);
    });

    // Memorization points routes
    Route::prefix('memorization')->group(function () {
        // Admin routes
        Route::middleware('role:admin')->group(function () {
            Route::post('points', [MemorizationController::class, 'storePoints']);
            Route::put('points/{point}', [MemorizationController::class, 'updatePoints']);
            Route::delete('points/{point}', [MemorizationController::class, 'deletePoints']);
            Route::get('students/{student}/points', [MemorizationController::class, 'getStudentPoints']);
        });
        
        // Parent routes
        Route::get('children/{student}/points', [MemorizationController::class, 'getChildPoints']);
        Route::get('children/{student}/summary', [MemorizationController::class, 'getChildSummary']);
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::post('{notification}/mark-read', [NotificationController::class, 'markAsRead']);
        Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::get('settings', [NotificationController::class, 'getSettings']);
        Route::put('settings', [NotificationController::class, 'updateSettings']);
        
        // Admin only routes
        Route::middleware('role:admin')->group(function () {
            Route::post('create', [NotificationController::class, 'createNotification']);
            Route::post('bulk', [NotificationController::class, 'sendBulkNotification']);
        });
    });

    // Circle routes
    Route::prefix('circles')->group(function () {
        Route::get('/', [CircleController::class, 'index']);
        Route::get('available', [CircleController::class, 'getAvailable']);
        Route::get('{circle}', [CircleController::class, 'show']);
        Route::get('{circle}/students', [CircleController::class, 'getStudents']);
        Route::get('{circle}/statistics', [CircleController::class, 'getStatistics']);
        
        // Admin only routes
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [CircleController::class, 'store']);
            Route::put('{circle}', [CircleController::class, 'update']);
            Route::delete('{circle}', [CircleController::class, 'destroy']);
            Route::post('{circle}/enroll', [CircleController::class, 'enrollStudent']);
            Route::post('{circle}/remove', [CircleController::class, 'removeStudent']);
        });
    });

    // Teacher routes
    Route::prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index']);
        Route::get('{teacher}', [TeacherController::class, 'show']);
        Route::get('{teacher}/circles', [TeacherController::class, 'getCircles']);
        Route::get('{teacher}/students', [TeacherController::class, 'getStudents']);
        Route::get('{teacher}/statistics', [TeacherController::class, 'getStatistics']);
        Route::get('{teacher}/performance', [TeacherController::class, 'getPerformanceReport']);
        
        // Admin only routes
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [TeacherController::class, 'store']);
            Route::put('{teacher}', [TeacherController::class, 'update']);
            Route::delete('{teacher}', [TeacherController::class, 'destroy']);
        });
    });

    // Attendance routes (Admin only)
    Route::prefix('attendance')->middleware('role:admin')->group(function () {
        Route::get('/', [AttendanceController::class, 'index']);
        Route::post('/', [AttendanceController::class, 'store']);
        Route::put('{attendance}', [AttendanceController::class, 'update']);
        Route::delete('{attendance}', [AttendanceController::class, 'destroy']);
        Route::post('bulk', [AttendanceController::class, 'recordBulkAttendance']);
        Route::get('circle/{circle}', [AttendanceController::class, 'getCircleAttendance']);
        Route::get('student/{student}', [AttendanceController::class, 'getStudentAttendance']);
        Route::get('statistics', [AttendanceController::class, 'getStatistics']);
        Route::get('report', [AttendanceController::class, 'getReport']);
    });

    // General user route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Health check route
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});


// Mobile API Routes
Route::prefix('mobile')->group(function () {
    
    // Mobile Authentication
    Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'mobileLogin']);
    
    // Protected mobile routes
    Route::middleware('auth:sanctum')->group(function () {
        
        // Mobile Teacher routes
        Route::prefix('teacher')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Api\TeacherMobileController::class, 'dashboard']);
            Route::get('/sessions', [App\Http\Controllers\Api\TeacherMobileController::class, 'sessions']);
            Route::get('/sessions/{sessionId}', [App\Http\Controllers\Api\TeacherMobileController::class, 'sessionDetails']);
            Route::post('/sessions', [App\Http\Controllers\Api\TeacherMobileController::class, 'createSession']);
            Route::post('/sessions/{sessionId}/start', [App\Http\Controllers\Api\TeacherMobileController::class, 'startSession']);
            Route::post('/sessions/{sessionId}/end', [App\Http\Controllers\Api\TeacherMobileController::class, 'endSession']);
            
            // Mobile Attendance routes for teachers
            Route::post('/sessions/{sessionId}/attendance', [App\Http\Controllers\Api\AttendanceMobileController::class, 'saveAttendance']);
            Route::get('/sessions/{sessionId}/attendance', [App\Http\Controllers\Api\AttendanceMobileController::class, 'getSessionAttendance']);
            Route::get('/students/{studentId}/attendance', [App\Http\Controllers\Api\AttendanceMobileController::class, 'getStudentAttendanceHistory']);
        });
        
        // Mobile Parent routes
        Route::prefix('parent')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Api\ParentMobileController::class, 'dashboard']);
            Route::get('/children', [App\Http\Controllers\Api\ParentMobileController::class, 'getChildren']);
            Route::get('/children/{childId}', [App\Http\Controllers\Api\ParentMobileController::class, 'getChildDetails']);
            Route::get('/reports/attendance', [App\Http\Controllers\Api\ParentMobileController::class, 'getAttendanceReports']);
            Route::get('/reports/points', [App\Http\Controllers\Api\ParentMobileController::class, 'getPointsReports']);
            
            // Mobile Attendance routes for parents
            Route::get('/children/{childId}/attendance', [App\Http\Controllers\Api\AttendanceMobileController::class, 'getStudentAttendanceHistory']);
        });
    });
});

