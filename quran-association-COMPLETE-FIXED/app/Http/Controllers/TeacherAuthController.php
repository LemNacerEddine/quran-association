<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TeacherAuthController extends Controller
{
    public function showLogin()
    {
        return view('teacher-auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string|size:4'
        ]);

        $teacher = Teacher::where('phone', $request->phone)
                         ->where('password', $request->password)
                         ->where('is_active', true)
                         ->first();

        if ($teacher) {
            Session::put('teacher_id', $teacher->id);
            Session::put('teacher_name', $teacher->name);
            
            $teacher->updateLastLogin();
            
            return redirect()->route('teacher.dashboard');
        }

        return back()->withErrors([
            'login' => 'رقم الهاتف أو كود الدخول غير صحيح'
        ])->withInput();
    }

    public function dashboard()
    {
        $teacherId = Session::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        $teacher = Teacher::with(['circles.students'])->find($teacherId);
        
        if (!$teacher) {
            Session::forget(['teacher_id', 'teacher_name']);
            return redirect()->route('teacher.login');
        }

        // إحصائيات المعلم
        $circles = $teacher->circles()->with('students')->get();
        $totalStudents = $circles->sum(function($circle) {
            return $circle->students->count();
        });

        // Get real sessions from database
        $circleIds = $circles->pluck('id');
        
        $todaySessions = \App\Models\Session::with(['circle', 'attendances'])
            ->whereIn('circle_id', $circleIds)
            ->whereDate('session_date', today())
            ->get();

        $upcomingSessions = \App\Models\Session::with(['circle', 'attendances'])
            ->whereIn('circle_id', $circleIds)
            ->where('session_date', '>', today())
            ->orderBy('session_date')
            ->get();

        $pastSessions = \App\Models\Session::with(['circle', 'attendances'])
            ->whereIn('circle_id', $circleIds)
            ->where('session_date', '<', today())
            ->where('status', '!=', 'completed')
            ->orderBy('session_date', 'desc')
            ->get();

        $completedSessions = \App\Models\Session::with(['circle', 'attendances'])
            ->whereIn('circle_id', $circleIds)
            ->where('status', 'completed')
            ->orderBy('session_date', 'desc')
            ->get();

        $stats = [
            'circles' => $circles->count(),
            'students' => $totalStudents,
            'today_sessions' => $todaySessions->count(),
            'completed_sessions' => $completedSessions->count(),
        ];

        return view('teacher-auth.dashboard', compact(
            'teacher', 
            'circles', 
            'stats',
            'todaySessions',
            'upcomingSessions', 
            'pastSessions', 
            'completedSessions'
        ));
    }

    public function logout()
    {
        Session::forget(['teacher_id', 'teacher_name']);
        return redirect()->route('teacher.login')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    public function profile()
    {
        $teacherId = Session::get('teacher_id');
        
        if (!$teacherId) {
            return redirect()->route('teacher.login');
        }

        $teacher = Teacher::with(['circles.students'])->find($teacherId);
        
        return view('teacher-auth.profile', compact('teacher'));
    }
}

