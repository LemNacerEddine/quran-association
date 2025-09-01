<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index()
    {
        $teachers = Teacher::with('circles')
            ->latest()
            ->paginate(15);

        return view('teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:teachers,phone|regex:/^0[5-9][0-9]{8}$/',
            'email' => 'nullable|email|unique:teachers,email',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'qualification' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'specialization' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        // إنشاء كود الدخول من آخر 4 أرقام من رقم الهاتف
        $loginCode = substr($request->phone, -4);

        $teacherData = $request->all();
        $teacherData['password'] = $loginCode; // حفظ كود الدخول في حقل password
        $teacherData['is_active'] = $teacherData['is_active'] ?? true;
        $teacherData['gender'] = $teacherData['gender'] ?? 'male'; // قيمة افتراضية للجنس
        
        // تعيين تاريخ التوظيف لتاريخ اليوم إذا لم يتم تحديده
        if (empty($teacherData['hire_date'])) {
            $teacherData['hire_date'] = now()->format('Y-m-d');
        }

        Teacher::create($teacherData);

        return redirect()->route('teachers.index')
            ->with('success', 'تم إضافة المعلم بنجاح. كود الدخول: ' . $loginCode);
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher)
    {
        $teacher->load(['circles', 'circles.students']);
        
        return view('teachers.show', compact('teacher'));
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified teacher.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:teachers,phone,' . $teacher->id,
            'password' => 'required|string|size:4',
            'email' => 'nullable|email|unique:teachers,email,' . $teacher->id,
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'qualification' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'specialization' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $teacherData = $request->all();
        $teacherData['is_active'] = $teacherData['is_active'] ?? true;

        $teacher->update($teacherData);

        return redirect()->route('teachers.index')
            ->with('success', 'تم تحديث بيانات المعلم بنجاح.');
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(Teacher $teacher)
    {
        // التحقق من وجود حلقات مرتبطة بالمعلم
        if ($teacher->circles()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف المعلم لوجود حلقات مرتبطة به. يرجى إعادة تعيين الحلقات لمعلم آخر أولاً.']);
        }

        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'تم حذف المعلم بنجاح.');
    }
}

