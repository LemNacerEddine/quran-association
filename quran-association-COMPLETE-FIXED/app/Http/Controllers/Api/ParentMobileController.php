<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParentMobileController extends Controller
{
    /**
     * Get parent dashboard data
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get children for this parent
            $children = Student::where('parent_phone', $user->phone)
                ->with(['circle.teacher'])
                ->get();

            if ($children->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد أطفال مسجلين لهذا الرقم'
                ], 404);
            }

            $totalChildren = $children->count();
            $studentIds = $children->pluck('id');

            // Calculate overall attendance percentage for this month
            $thisMonth = Carbon::now()->startOfMonth();
            $attendanceStats = Attendance::whereIn('student_id', $studentIds)
                ->whereHas('session', function($q) use ($thisMonth) {
                    $q->where('session_date', '>=', $thisMonth);
                })
                ->selectRaw('
                    COUNT(*) as total_records,
                    SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count
                ')
                ->first();

            $attendancePercentage = $attendanceStats->total_records > 0 
                ? round(($attendanceStats->present_count / $attendanceStats->total_records) * 100, 1)
                : 0;

            // Calculate total points this month
            $totalPoints = Attendance::whereIn('student_id', $studentIds)
                ->whereHas('session', function($q) use ($thisMonth) {
                    $q->where('session_date', '>=', $thisMonth);
                })
                ->sum('total_points');

            // Get sessions count this month
            $sessionsThisMonth = Session::whereHas('circle.students', function($q) use ($studentIds) {
                $q->whereIn('students.id', $studentIds);
            })->where('session_date', '>=', $thisMonth)->count();

            // Get children with their latest activity
            $childrenData = $children->map(function($child) {
                $latestAttendance = Attendance::where('student_id', $child->id)
                    ->with('session')
                    ->orderBy('created_at', 'desc')
                    ->first();

                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'age' => $child->age,
                    'gender' => $child->gender,
                    'circle' => [
                        'id' => $child->circle->id,
                        'name' => $child->circle->name,
                        'teacher' => [
                            'id' => $child->circle->teacher->id,
                            'name' => $child->circle->teacher->name
                        ]
                    ],
                    'latest_activity' => $latestAttendance ? [
                        'session_title' => $latestAttendance->session->title,
                        'session_date' => $latestAttendance->session->session_date,
                        'status' => $latestAttendance->status,
                        'total_points' => $latestAttendance->total_points,
                        'notes' => $latestAttendance->notes
                    ] : null
                ];
            });

            // Get recent activities
            $recentActivities = Attendance::whereIn('student_id', $studentIds)
                ->with(['student', 'session'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($attendance) {
                    return [
                        'type' => 'attendance',
                        'student_name' => $attendance->student->name,
                        'session_title' => $attendance->session->title,
                        'session_date' => $attendance->session->session_date,
                        'status' => $attendance->status,
                        'total_points' => $attendance->total_points,
                        'notes' => $attendance->notes,
                        'recorded_at' => $attendance->recorded_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'parent' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone
                    ],
                    'stats' => [
                        'total_children' => $totalChildren,
                        'attendance_percentage' => $attendancePercentage,
                        'total_points' => $totalPoints,
                        'sessions_this_month' => $sessionsThisMonth
                    ],
                    'children' => $childrenData,
                    'recent_activities' => $recentActivities
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات لوحة التحكم'
            ], 500);
        }
    }

    /**
     * Get parent's children
     */
    public function getChildren(Request $request)
    {
        try {
            $user = $request->user();
            
            $children = Student::where('parent_phone', $user->phone)
                ->with(['circle.teacher'])
                ->get();

            if ($children->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد أطفال مسجلين لهذا الرقم'
                ], 404);
            }

            // Get detailed stats for each child
            $childrenData = $children->map(function($child) {
                // Get attendance stats for this month
                $thisMonth = Carbon::now()->startOfMonth();
                $attendanceStats = Attendance::where('student_id', $child->id)
                    ->whereHas('session', function($q) use ($thisMonth) {
                        $q->where('session_date', '>=', $thisMonth);
                    })
                    ->selectRaw('
                        COUNT(*) as total_sessions,
                        SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                        SUM(attendance_points) as attendance_points,
                        SUM(memorization_points) as memorization_points,
                        SUM(total_points) as total_points
                    ')
                    ->first();

                $attendancePercentage = $attendanceStats->total_sessions > 0 
                    ? round(($attendanceStats->present_count / $attendanceStats->total_sessions) * 100, 1)
                    : 0;

                // Get latest teacher notes
                $latestNotes = Attendance::where('student_id', $child->id)
                    ->whereNotNull('notes')
                    ->with('session')
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get()
                    ->map(function($attendance) {
                        return [
                            'session_date' => $attendance->session->session_date,
                            'session_title' => $attendance->session->title,
                            'notes' => $attendance->notes
                        ];
                    });

                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'age' => $child->age,
                    'gender' => $child->gender,
                    'phone' => $child->phone,
                    'circle' => [
                        'id' => $child->circle->id,
                        'name' => $child->circle->name,
                        'teacher' => [
                            'id' => $child->circle->teacher->id,
                            'name' => $child->circle->teacher->name,
                            'phone' => $child->circle->teacher->phone
                        ]
                    ],
                    'stats' => [
                        'total_sessions' => $attendanceStats->total_sessions ?? 0,
                        'present_count' => $attendanceStats->present_count ?? 0,
                        'attendance_percentage' => $attendancePercentage,
                        'attendance_points' => $attendanceStats->attendance_points ?? 0,
                        'memorization_points' => $attendanceStats->memorization_points ?? 0,
                        'total_points' => $attendanceStats->total_points ?? 0
                    ],
                    'latest_notes' => $latestNotes
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'children' => $childrenData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب بيانات الأطفال'
            ], 500);
        }
    }

    /**
     * Get child details
     */
    public function getChildDetails(Request $request, $childId)
    {
        try {
            $user = $request->user();
            
            $child = Student::where('id', $childId)
                ->where('parent_phone', $user->phone)
                ->with(['circle.teacher'])
                ->first();

            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'الطفل غير موجود أو غير مصرح لك بالوصول إليه'
                ], 404);
            }

            // Get comprehensive stats for different periods
            $periods = [
                'this_week' => Carbon::now()->startOfWeek(),
                'this_month' => Carbon::now()->startOfMonth(),
                'this_quarter' => Carbon::now()->startOfQuarter(),
            ];

            $stats = [];
            foreach ($periods as $period => $startDate) {
                $periodStats = Attendance::where('student_id', $child->id)
                    ->whereHas('session', function($q) use ($startDate) {
                        $q->where('session_date', '>=', $startDate);
                    })
                    ->selectRaw('
                        COUNT(*) as total_sessions,
                        SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                        SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count,
                        SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late_count,
                        SUM(attendance_points) as attendance_points,
                        SUM(memorization_points) as memorization_points,
                        SUM(total_points) as total_points,
                        AVG(total_points) as average_points
                    ')
                    ->first();

                $attendancePercentage = $periodStats->total_sessions > 0 
                    ? round(($periodStats->present_count / $periodStats->total_sessions) * 100, 1)
                    : 0;

                $stats[$period] = [
                    'total_sessions' => $periodStats->total_sessions ?? 0,
                    'present_count' => $periodStats->present_count ?? 0,
                    'absent_count' => $periodStats->absent_count ?? 0,
                    'late_count' => $periodStats->late_count ?? 0,
                    'attendance_percentage' => $attendancePercentage,
                    'attendance_points' => $periodStats->attendance_points ?? 0,
                    'memorization_points' => $periodStats->memorization_points ?? 0,
                    'total_points' => $periodStats->total_points ?? 0,
                    'average_points' => round($periodStats->average_points ?? 0, 1)
                ];
            }

            // Get recent attendance records
            $recentAttendance = Attendance::where('student_id', $child->id)
                ->with('session')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($attendance) {
                    return [
                        'id' => $attendance->id,
                        'session' => [
                            'id' => $attendance->session->id,
                            'title' => $attendance->session->title,
                            'session_date' => $attendance->session->session_date
                        ],
                        'status' => $attendance->status,
                        'attendance_points' => $attendance->attendance_points,
                        'memorization_points' => $attendance->memorization_points,
                        'total_points' => $attendance->total_points,
                        'notes' => $attendance->notes,
                        'recorded_at' => $attendance->recorded_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'child' => [
                        'id' => $child->id,
                        'name' => $child->name,
                        'age' => $child->age,
                        'gender' => $child->gender,
                        'phone' => $child->phone,
                        'circle' => [
                            'id' => $child->circle->id,
                            'name' => $child->circle->name,
                            'teacher' => [
                                'id' => $child->circle->teacher->id,
                                'name' => $child->circle->teacher->name,
                                'phone' => $child->circle->teacher->phone
                            ]
                        ]
                    ],
                    'statistics' => $stats,
                    'recent_attendance' => $recentAttendance
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل الطفل'
            ], 500);
        }
    }

    /**
     * Get attendance reports for parent's children
     */
    public function getAttendanceReports(Request $request)
    {
        try {
            $user = $request->user();
            $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
            $childId = $request->get('child_id');

            $query = Student::where('parent_phone', $user->phone);
            
            if ($childId) {
                $query->where('id', $childId);
            }

            $children = $query->with(['circle.teacher'])->get();

            if ($children->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد أطفال مسجلين لهذا الرقم'
                ], 404);
            }

            $reports = $children->map(function($child) use ($startDate, $endDate) {
                $attendance = Attendance::where('student_id', $child->id)
                    ->whereHas('session', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('session_date', [$startDate, $endDate]);
                    })
                    ->with('session')
                    ->get();

                $stats = [
                    'total_sessions' => $attendance->count(),
                    'present_count' => $attendance->where('status', 'present')->count(),
                    'absent_count' => $attendance->where('status', 'absent')->count(),
                    'late_count' => $attendance->where('status', 'late')->count(),
                    'excused_count' => $attendance->where('status', 'excused')->count(),
                    'total_points' => $attendance->sum('total_points'),
                    'attendance_points' => $attendance->sum('attendance_points'),
                    'memorization_points' => $attendance->sum('memorization_points'),
                    'average_points' => $attendance->avg('total_points') ?? 0
                ];

                $stats['attendance_percentage'] = $stats['total_sessions'] > 0 
                    ? round(($stats['present_count'] / $stats['total_sessions']) * 100, 1) 
                    : 0;

                return [
                    'child' => [
                        'id' => $child->id,
                        'name' => $child->name,
                        'circle' => [
                            'id' => $child->circle->id,
                            'name' => $child->circle->name,
                            'teacher_name' => $child->circle->teacher->name
                        ]
                    ],
                    'statistics' => $stats,
                    'attendance_details' => $attendance->map(function($record) {
                        return [
                            'session_date' => $record->session->session_date,
                            'session_title' => $record->session->title,
                            'status' => $record->status,
                            'total_points' => $record->total_points,
                            'notes' => $record->notes
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ],
                    'reports' => $reports
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تقارير الحضور'
            ], 500);
        }
    }

    /**
     * Get points reports for parent's children
     */
    public function getPointsReports(Request $request)
    {
        try {
            $user = $request->user();
            $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
            $childId = $request->get('child_id');

            $query = Student::where('parent_phone', $user->phone);
            
            if ($childId) {
                $query->where('id', $childId);
            }

            $children = $query->with(['circle.teacher'])->get();

            if ($children->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد أطفال مسجلين لهذا الرقم'
                ], 404);
            }

            $reports = $children->map(function($child) use ($startDate, $endDate) {
                $attendance = Attendance::where('student_id', $child->id)
                    ->whereHas('session', function($q) use ($startDate, $endDate) {
                        $q->whereBetween('session_date', [$startDate, $endDate]);
                    })
                    ->with('session')
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Calculate points breakdown
                $pointsBreakdown = [
                    'total_points' => $attendance->sum('total_points'),
                    'attendance_points' => $attendance->sum('attendance_points'),
                    'memorization_points' => $attendance->sum('memorization_points'),
                    'average_total' => $attendance->avg('total_points') ?? 0,
                    'average_attendance' => $attendance->avg('attendance_points') ?? 0,
                    'average_memorization' => $attendance->avg('memorization_points') ?? 0,
                    'best_session' => $attendance->max('total_points') ?? 0,
                    'sessions_with_full_memorization' => $attendance->where('memorization_points', 5)->count(),
                    'sessions_with_no_memorization' => $attendance->where('memorization_points', 0)->count()
                ];

                // Get daily points for chart
                $dailyPoints = $attendance->groupBy(function($item) {
                    return $item->session->session_date;
                })->map(function($dayAttendance) {
                    return [
                        'date' => $dayAttendance->first()->session->session_date,
                        'total_points' => $dayAttendance->sum('total_points'),
                        'attendance_points' => $dayAttendance->sum('attendance_points'),
                        'memorization_points' => $dayAttendance->sum('memorization_points'),
                        'sessions_count' => $dayAttendance->count()
                    ];
                })->values();

                return [
                    'child' => [
                        'id' => $child->id,
                        'name' => $child->name,
                        'circle' => [
                            'id' => $child->circle->id,
                            'name' => $child->circle->name,
                            'teacher_name' => $child->circle->teacher->name
                        ]
                    ],
                    'points_breakdown' => $pointsBreakdown,
                    'daily_points' => $dailyPoints,
                    'recent_sessions' => $attendance->take(10)->map(function($record) {
                        return [
                            'session_date' => $record->session->session_date,
                            'session_title' => $record->session->title,
                            'attendance_points' => $record->attendance_points,
                            'memorization_points' => $record->memorization_points,
                            'total_points' => $record->total_points,
                            'status' => $record->status
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'period' => [
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ],
                    'reports' => $reports
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تقارير النقاط'
            ], 500);
        }
    }
}

