<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;

class CircleController extends Controller
{
    /**
     * Display a listing of circles.
     */
    public function index()
    {
        $circles = Circle::with(['teacher', 'students'])
            ->latest()
            ->paginate(15);

        return view('circles.index', compact('circles'));
    }

    /**
     * Show the form for creating a new circle.
     */
    public function create()
    {
        $teachers = Teacher::active()->get();
        $students = Student::active()->get();
        
        return view('circles.create', compact('teachers', 'students'));
    }

    /**
     * Store a newly created circle.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:teachers,id',
            'level' => 'required|string|max:100',
            'age_group' => 'required|string|max:100',
            'gender' => 'required|in:male,female,mixed',
            'max_students' => 'required|integer|min:1|max:50',
            'location' => 'required|string|max:255',
            'schedule_days' => 'required|array',
            'schedule_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'students' => 'nullable|array',
            'students.*' => 'exists:students,id',
        ]);

        $circle = Circle::create($request->except('students'));

        // ربط الطلاب المحددين بالحلقة
        if ($request->has('students') && is_array($request->students)) {
            $studentsData = [];
            foreach ($request->students as $studentId) {
                $studentsData[$studentId] = [
                    'enrolled_at' => now(),
                    'is_active' => true,
                    'notes' => 'تسجيل عند إنشاء الحلقة',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $circle->students()->attach($studentsData);
        }

        return redirect()->route('circles.index')
            ->with('success', 'تم إنشاء الحلقة بنجاح.');
    }

    /**
     * Display the specified circle.
     */
    public function show(Circle $circle)
    {
        $circle->load(['teacher', 'students', 'schedules', 'sessions']);
        
        return view('circles.show', compact('circle'));
    }

    /**
     * Show the form for editing the specified circle.
     */
    public function edit(Circle $circle)
    {
        $teachers = Teacher::active()->get();
        $students = Student::active()->get();
        $circle->load('students');
        
        return view('circles.edit_improved', compact('circle', 'teachers', 'students'));
    }

    /**
     * Update the specified circle.
     */
    public function update(Request $request, Circle $circle)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'teacher_id' => 'nullable|exists:teachers,id',
                'level' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:255',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i',
                'max_students' => 'nullable|integer|min:1',
                'is_active' => 'nullable',
                'schedule_days' => 'nullable|array',
                'students' => 'nullable|array',
                'students.*' => 'exists:students,id',
            ]);

            // معالجة أيام الحلقة - تحويل من الإنجليزية إلى العربية
            $scheduleDays = '';
            if ($request->has('schedule_days') && is_array($request->schedule_days)) {
                $englishToArabic = [
                    'sunday' => 'الأحد',
                    'monday' => 'الاثنين',
                    'tuesday' => 'الثلاثاء',
                    'wednesday' => 'الأربعاء',
                    'thursday' => 'الخميس',
                    'friday' => 'الجمعة',
                    'saturday' => 'السبت'
                ];
                
                $arabicDays = [];
                foreach ($request->schedule_days as $day) {
                    if (isset($englishToArabic[$day])) {
                        $arabicDays[] = $englishToArabic[$day];
                    }
                }
                $scheduleDays = implode(',', $arabicDays);
            }

            // إصلاح معالجة is_active
            $isActive = (int) $request->input('is_active', 0) === 1;

            // تحديث البيانات باستخدام Eloquent
            $circle->update([
                'name' => $request->name,
                'description' => $request->description,
                'teacher_id' => $request->teacher_id,
                'level' => $request->level,
                'location' => $request->location,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'max_students' => $request->max_students,
                'is_active' => $isActive,
                'schedule_days' => $scheduleDays,
            ]);

            // تحديث الطلاب المرتبطين بالحلقة
            // دائماً نقوم بتحديث الطلاب حتى لو كانت القائمة فارغة
            $circle->students()->detach(); // حذف جميع الطلاب الحاليين
            
            // إضافة الطلاب الجدد إذا تم تحديدهم
            if ($request->has('students') && is_array($request->students) && !empty($request->students)) {
                $studentsData = [];
                foreach ($request->students as $studentId) {
                    $studentsData[$studentId] = [
                        'enrolled_at' => now(),
                        'is_active' => true,
                        'notes' => 'تحديث عند تعديل الحلقة',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $circle->students()->attach($studentsData);
            }

            // تسجيل للتأكد من الحفظ
            \Log::info('Circle updated successfully', [
                'circle_id' => $circle->id,
                'circle_name' => $circle->name,
                'schedule_days_saved' => $scheduleDays,
                'request_schedule_days' => $request->schedule_days,
                'is_active_saved' => $isActive,
                'students_in_request' => $request->has('students') ? $request->students : null,
                'students_count_before' => $circle->students()->count(),
                'students_count_after' => $request->has('students') && is_array($request->students) ? count($request->students) : 0,
                'is_ajax' => $request->ajax()
            ]);

            // إعادة تحميل العلاقات للتأكد من التحديث
            $circle->load('students');

            // التحقق من نوع الطلب - إعطاء أولوية للـ AJAX
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث الحلقة بنجاح',
                    'circle_name' => $circle->name,
                    'schedule_days_saved' => $scheduleDays,
                    'is_active_saved' => $isActive,
                    'students_updated' => true,
                    'students_count' => $circle->students()->count(),
                    'students_list' => $circle->students->pluck('name', 'id')->toArray(),
                    'redirect_url' => route('circles.index')
                ]);
            }

            return redirect()->route('circles.index')
                ->with('success', 'تم تحديث الحلقة بنجاح. الأيام المحفوظة: ' . $scheduleDays);
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error updating circle', [
                'circle_id' => $circle->id,
                'errors' => $e->errors(),
                'is_ajax' => $request->ajax()
            ]);

            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
                
        } catch (\Exception $e) {
            \Log::error('Error updating circle', [
                'circle_id' => $circle->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'is_ajax' => $request->ajax()
            ]);

            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified circle.
     */
    public function destroy(Circle $circle)
    {
        try {
            // التحقق من وجود طلاب نشطين في الحلقة
            $activeStudentsCount = $circle->students()->count();
            if ($activeStudentsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "لا يمكن حذف الحلقة لوجود {$activeStudentsCount} طلاب مسجلين بها. يرجى نقل الطلاب إلى حلقة أخرى أولاً."
                ], 400);
            }

            // التحقق من وجود جلسات مجدولة
            $sessionsCount = $circle->sessions()->count();
            if ($sessionsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "لا يمكن حذف الحلقة لوجود {$sessionsCount} جلسة مجدولة لها. يرجى حذف الجلسات أولاً."
                ], 400);
            }

            // حفظ اسم الحلقة قبل الحذف
            $circleName = $circle->name;

            // حذف الحلقة
            $circle->delete();

            // إرجاع استجابة JSON للطلبات AJAX
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "تم حذف الحلقة '{$circleName}' بنجاح."
                ]);
            }

            // إرجاع redirect للطلبات العادية
            return redirect()->route('circles.index')
                ->with('success', "تم حذف الحلقة '{$circleName}' بنجاح.");

        } catch (\Exception $e) {
            \Log::error('خطأ في حذف الحلقة: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف الحلقة. يرجى المحاولة مرة أخرى.'
                ], 500);
            }

            return back()->withErrors(['error' => 'حدث خطأ أثناء حذف الحلقة. يرجى المحاولة مرة أخرى.']);
        }
    }
}

