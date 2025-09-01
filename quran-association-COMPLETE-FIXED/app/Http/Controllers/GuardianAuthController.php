<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuardianAuthController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول لأولياء الأمور
     */
    public function showLoginForm()
    {
        return view('guardian-auth.login');
    }

    /**
     * معالجة تسجيل الدخول
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'access_code' => 'required|string|size:4',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'access_code.required' => 'كود الدخول مطلوب',
            'access_code.size' => 'كود الدخول يجب أن يكون 4 أرقام',
        ]);

        // البحث عن ولي الأمر
        $guardian = Guardian::where('phone', $request->phone)
                          ->where('is_active', true)
                          ->first();

        if (!$guardian) {
            return back()->withInput()->withErrors([
                'phone' => 'رقم الهاتف غير مسجل أو الحساب غير نشط'
            ]);
        }

        // التحقق من كود الدخول
        if (!$guardian->verifyAccessCode($request->access_code)) {
            return back()->withInput()->withErrors([
                'access_code' => 'كود الدخول غير صحيح'
            ]);
        }

        // تسجيل الدخول
        Auth::guard('guardian')->login($guardian, $request->boolean('remember'));

        // تحديث آخر دخول
        $guardian->update([
            'last_login_at' => now(),
        ]);

        return redirect()->intended(route('guardian.dashboard'))
                        ->with('success', 'مرحباً بك ' . $guardian->name);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        Auth::guard('guardian')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('guardian.login')
                        ->with('success', 'تم تسجيل الخروج بنجاح');
    }

    /**
     * لوحة تحكم ولي الأمر
     */
    public function dashboard()
    {
        // الحصول على ولي الأمر المسجل دخوله
        $guardian = Auth::guard('guardian')->user();
        
        if (!$guardian) {
            return redirect()->route('guardian.login')
                           ->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        // تحميل العلاقات المطلوبة
        $guardian->load(['students.circle', 'students.circle.teacher']);

        // حساب الإحصائيات الصحيحة لكل طالب
        $studentsWithStats = $guardian->students->map(function ($student) {
            // إحصائيات الحضور من جدول attendance فقط (نفس مصدر لوحة الإدارة)
            $attendanceStats = \DB::table('attendance')
                ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
                ->where('attendance.student_id', $student->id)
                ->selectRaw('
                    COUNT(*) as total_sessions,
                    SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                    SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count,
                    ROUND(
                        CASE 
                            WHEN COUNT(*) > 0 THEN 
                                (SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) * 100.0 / COUNT(*))
                            ELSE 0 
                        END, 1
                    ) as attendance_percentage,
                    SUM(COALESCE(attendance.final_points, 0)) as total_points,
                    ROUND(AVG(COALESCE(attendance.final_points, 0)), 1) as avg_points
                ')
                ->first();

            // إضافة الإحصائيات للطالب
            $student->attendance_stats = $attendanceStats;
            
            // حساب الإحصائيات المدمجة (نفس طريقة لوحة الإدارة)
            $student->total_sessions = $attendanceStats->total_sessions ?? 0;
            $student->present_count = $attendanceStats->present_count ?? 0;
            $student->absent_count = $attendanceStats->absent_count ?? 0;
            $student->late_count = $attendanceStats->late_count ?? 0;
            $student->attendance_percentage = $attendanceStats->attendance_percentage ?? 0;
            $student->total_points = $attendanceStats->total_points ?? 0;
            $student->avg_points = $attendanceStats->avg_points ?? 0;
            
            return $student;
        });

        // إحصائيات عامة
        $stats = [
            'total_students' => $guardian->students->count(),
            'primary_students' => $guardian->students->where('pivot.is_primary', true)->count(),
            'active_circles' => $guardian->students->whereNotNull('circle')->count(),
            'total_sessions' => $studentsWithStats->sum('total_sessions'),
            'total_attendance' => $studentsWithStats->sum('present_count'),
            'total_absences' => $studentsWithStats->sum('absent_count'),
            'total_points' => $studentsWithStats->sum('total_points'),
            'avg_attendance_rate' => $studentsWithStats->count() > 0 ? 
                round($studentsWithStats->avg('attendance_percentage'), 1) : 0,
        ];

        return view('guardian-auth.dashboard', compact('guardian', 'stats', 'studentsWithStats'));
    }

    /**
     * عرض ملف ولي الأمر الشخصي
     */
    public function profile()
    {
        // مؤقتاً: الحصول على أول ولي أمر للاختبار
        $guardian = Guardian::with(['students.circle', 'students.circle.teacher'])->first();
        
        if (!$guardian) {
            return redirect()->route('guardian.login')
                           ->with('error', 'لا توجد بيانات أولياء أمور');
        }

        return view('guardian-auth.profile', compact('guardian'));
    }

    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $guardian = Auth::guard('guardian')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'job' => 'nullable|string|max:255',
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
        ]);

        $guardian->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'job' => $request->job,
        ]);

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * عرض تفاصيل الطالب
     */
    public function showStudent($studentId)
    {
        // مؤقتاً: الحصول على أول ولي أمر للاختبار
        $guardian = Guardian::with(['students.circle', 'students.circle.teacher'])->first();
        
        if (!$guardian) {
            return redirect()->route('guardian.login')
                           ->with('error', 'لا توجد بيانات أولياء أمور');
        }
        
        // التأكد من أن الطالب ينتمي لولي الأمر
        $student = $guardian->students()->with(['circle', 'circle.teacher'])->find($studentId);
        
        if (!$student) {
            abort(404, 'الطالب غير موجود أو غير مرتبط بحسابك');
        }

        // إحصائيات الحضور الحقيقية من قاعدة البيانات
        $attendanceStats = \DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('attendance.student_id', $studentId)
            ->selectRaw('
                COUNT(*) as total_sessions,
                SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count,
                ROUND(
                    CASE 
                        WHEN COUNT(*) > 0 THEN 
                            (SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) * 100.0 / COUNT(*))
                        ELSE 0 
                    END, 1
                ) as attendance_percentage,
                SUM(COALESCE(attendance.final_points, 0)) as total_points,
                ROUND(AVG(COALESCE(attendance.final_points, 0)), 1) as avg_points
            ')
            ->first();

        // سجل الحضور الأخير الحقيقي من قاعدة البيانات
        $recentAttendance = \DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('circles', 'class_sessions.circle_id', '=', 'circles.id')
            ->where('attendance.student_id', $studentId)
            ->select([
                'attendance.*',
                'class_sessions.session_date',
                'class_sessions.actual_start_time',
                'class_sessions.actual_end_time',
                'circles.name as circle_name'
            ])
            ->orderBy('class_sessions.session_date', 'desc')
            ->limit(20)
            ->get();

        return view('guardian-auth.student', compact('guardian', 'student', 'attendanceStats', 'recentAttendance'));
    }

    /**
     * عرض تقارير الطالب
     */
    public function studentReports($studentId)
    {
        // مؤقتاً: الحصول على أول ولي أمر للاختبار
        $guardian = Guardian::with(['students.circle', 'students.circle.teacher'])->first();
        
        if (!$guardian) {
            return redirect()->route('guardian.login')
                           ->with('error', 'لا توجد بيانات أولياء أمور');
        }
        
        // التأكد من أن الطالب ينتمي لولي الأمر
        $student = $guardian->students()->with(['circle', 'circle.teacher'])->find($studentId);
        
        if (!$student) {
            abort(404, 'الطالب غير موجود أو غير مرتبط بحسابك');
        }

        //        // إحصائيات الطالب (من قاعدة البيانات الحقيقية)
        $stats = \DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('attendance.student_id', $student->id)
            ->selectRaw('
                COUNT(*) as total_sessions,
                SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) as late_count,
                SUM(COALESCE(attendance.final_points, 0)) as total_points,
                ROUND(AVG(COALESCE(attendance.final_points, 0)), 2) as average_points,
                ROUND(
                    CASE 
                        WHEN COUNT(*) > 0 THEN 
                            (SUM(CASE WHEN attendance.status = "present" THEN 1 ELSE 0 END) * 100.0 / COUNT(*))
                        ELSE 0 
                    END, 0
                ) as attendance_percentage,
                ROUND(
                    CASE 
                        WHEN COUNT(*) > 0 THEN 
                            (SUM(CASE WHEN attendance.status = "absent" THEN 1 ELSE 0 END) * 100.0 / COUNT(*))
                        ELSE 0 
                    END, 0
                ) as absent_percentage,
                ROUND(
                    CASE 
                        WHEN COUNT(*) > 0 THEN 
                            (SUM(CASE WHEN attendance.status = "late" THEN 1 ELSE 0 END) * 100.0 / COUNT(*))
                        ELSE 0 
                    END, 0
                ) as late_percentage
            ')
            ->first();

        // تحويل النتيجة إلى array
        $stats = (array) $stats;

        // حالات الغياب الحديثة (آخر أسبوعين)
        $recentAbsences = \DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->where('attendance.student_id', $student->id)
            ->where('attendance.status', 'absent')
            ->where('class_sessions.session_date', '>=', now()->subDays(14))
            ->select([
                'class_sessions.session_date as date',
                'attendance.notes as reason'
            ])
            ->orderBy('class_sessions.session_date', 'desc')
            ->get();

        // سجل الحضور التفصيلي (جميع السجلات)
        $attendanceRecords = \DB::table('attendance')
            ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
            ->leftJoin('circles', 'class_sessions.circle_id', '=', 'circles.id')
            ->where('attendance.student_id', $student->id)
            ->select([
                'attendance.*',
                'class_sessions.session_date as date',
                'class_sessions.actual_start_time as arrival_time',
                'class_sessions.actual_end_time',
                'circles.name as circle_name'
            ])
            ->orderBy('class_sessions.session_date', 'desc')
            ->get()
            ->map(function($record) {
                return (object)[
                    'date' => \Carbon\Carbon::parse($record->date),
                    'status' => $record->status,
                    'points' => $record->final_points ?? 0,
                    'arrival_time' => $record->arrival_time ? \Carbon\Carbon::parse($record->arrival_time)->format('H:i') : null,
                    'notes' => $record->notes ?? ($record->status == 'absent' ? 'غياب بعذر' : ($record->status == 'late' ? 'تأخر' : 'حضور جيد'))
                ];
            });

        return view('guardian-auth.student-reports', compact('guardian', 'student', 'stats', 'recentAbsences', 'attendanceRecords'));
    }

    /**
     * طلب إعادة تعيين كود الدخول
     */
    public function showResetCodeForm()
    {
        return view('guardian-auth.reset-code');
    }

    /**
     * معالجة طلب إعادة تعيين كود الدخول
     */
    public function resetCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'national_id' => 'required|string',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'national_id.required' => 'رقم الهوية مطلوب',
        ]);

        // البحث عن ولي الأمر
        $guardian = Guardian::where('phone', $request->phone)
                          ->where('national_id', $request->national_id)
                          ->where('is_active', true)
                          ->first();

        if (!$guardian) {
            return back()->withInput()->withErrors([
                'phone' => 'البيانات المدخلة غير صحيحة أو الحساب غير نشط'
            ]);
        }

        // إعادة تعيين كود الدخول
        $newCode = Guardian::generateAccessCode($guardian->phone);
        $guardian->update(['access_code' => $newCode]);

        // في التطبيق الحقيقي، يجب إرسال الكود عبر SMS
        // هنا سنعرضه في الصفحة للاختبار
        return back()->with('success', 'تم إعادة تعيين كود الدخول. الكود الجديد: ' . $newCode);
    }
}
