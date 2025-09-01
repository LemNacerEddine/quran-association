<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuardianController extends Controller
{
    /**
     * عرض قائمة أولياء الأمور
     */
    public function index(Request $request)
    {
        $query = Guardian::with(['students']);
        
        // البحث
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // فلترة حسب صلة القرابة
        if ($request->filled('relationship')) {
            $query->where('relationship', $request->relationship);
        }
        
        $guardians = $query->orderBy('name')->paginate(15);
        
        // إحصائيات
        $stats = [
            'total' => Guardian::count(),
            'active' => Guardian::where('is_active', true)->count(),
            'inactive' => Guardian::where('is_active', false)->count(),
            'fathers' => Guardian::where('relationship', 'father')->count(),
            'mothers' => Guardian::where('relationship', 'mother')->count(),
        ];
        
        return view('guardians.index', compact('guardians', 'stats'));
    }

    /**
     * عرض نموذج إضافة ولي أمر جديد
     */
    public function create()
    {
        // الحصول على الطلاب غير المرتبطين بأولياء أمور أو المرتبطين بولي الأمر الحالي فقط
        $students = Student::whereDoesntHave('guardians')
                          ->orWhereHas('guardians', function($query) {
                              // يمكن إضافة شروط إضافية هنا إذا لزم الأمر
                          })
                          ->with('circles.teacher')
                          ->orderBy('name')
                          ->get();
                          
        return view('guardians.create', compact('students'));
    }

    /**
     * حفظ ولي أمر جديد
     */
    public function store(Request $request)
    {
        // Debug: Log the request data
        \Log::info('Guardian creation request data:', $request->all());
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:guardians,phone|max:20',
            'email' => 'nullable|email|max:255',
            'national_id' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'job' => 'nullable|string|max:255',
            'relationship' => 'required|in:father,mother,guardian,other',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ], [
            'name.required' => 'اسم ولي الأمر مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مستخدم من قبل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'relationship.required' => 'صلة القرابة مطلوبة',
        ]);

        DB::beginTransaction();
        
        try {
            // إنشاء كود الدخول
            $accessCode = Guardian::generateAccessCode($request->phone);
            
            $guardian = Guardian::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'national_id' => $request->national_id,
                'address' => $request->address,
                'job' => $request->job,
                'access_code' => $accessCode,
                'relationship' => $request->relationship,
                'is_active' => $request->boolean('is_active', true),
                'notes' => $request->notes,
            ]);

            // ربط الطلاب المحددين
            if ($request->filled('students')) {
                foreach ($request->students as $studentId) {
                    $relationshipType = $request->input("relationship_types.{$studentId}", $request->relationship);
                    $isPrimary = $request->boolean("is_primary.{$studentId}", false);
                    
                    $guardian->students()->attach($studentId, [
                        'relationship_type' => $relationshipType,
                        'is_primary' => $isPrimary,
                    ]);
                }
            }

            DB::commit();
            
            return redirect()->route('guardians.index')
                           ->with('success', 'تم إضافة ولي الأمر بنجاح. كود الدخول: ' . $accessCode);
                           
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Guardian creation error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'حدث خطأ أثناء إضافة ولي الأمر: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل ولي أمر
     */
    public function show(Guardian $guardian)
    {
        // تحميل الطلاب مع الحلقات والمعلمين
        $guardian->load(['students.circles.teacher']);
        
        return view('guardians.show', compact('guardian'));
    }

    /**
     * عرض نموذج تعديل ولي الأمر
     */
    public function edit(Guardian $guardian)
    {
        // الحصول على الطلاب غير المرتبطين أو المرتبطين بولي الأمر الحالي
        $students = Student::whereDoesntHave('guardians')
                          ->orWhereHas('guardians', function($query) use ($guardian) {
                              $query->where('guardian_id', $guardian->id);
                          })
                          ->with('circles.teacher')
                          ->orderBy('name')
                          ->get();
                          
        return view('guardians.edit', compact('guardian', 'students'));
    }

    /**
     * تحديث بيانات ولي أمر
     */
    public function update(Request $request, Guardian $guardian)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:guardians,phone,' . $guardian->id,
            'email' => 'nullable|email|max:255',
            'national_id' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'job' => 'nullable|string|max:255',
            'relationship' => 'required|in:father,mother,guardian,other',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'students' => 'nullable|array',
            'students.*' => 'exists:students,id',
            'relationship_types' => 'nullable|array',
            'relationship_types.*' => 'in:father,mother,guardian,other',
            'is_primary' => 'nullable|array',
        ], [
            'name.required' => 'اسم ولي الأمر مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مستخدم من قبل',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'relationship.required' => 'صلة القرابة مطلوبة',
        ]);

        DB::beginTransaction();
        
        try {
            // تحديث كود الدخول إذا تغير رقم الهاتف
            $accessCode = $guardian->access_code;
            if ($guardian->phone !== $request->phone) {
                $accessCode = Guardian::generateAccessCode($request->phone);
            }
            
            $guardian->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'national_id' => $request->national_id,
                'address' => $request->address,
                'job' => $request->job,
                'access_code' => $accessCode,
                'relationship' => $request->relationship,
                'is_active' => $request->boolean('is_active', true),
                'notes' => $request->notes,
            ]);

            // إعادة ربط الطلاب
            $guardian->students()->detach();
            
            if ($request->filled('students')) {
                foreach ($request->students as $index => $studentId) {
                    $relationshipType = $request->relationship_types[$index] ?? $request->relationship;
                    $isPrimary = isset($request->is_primary[$index]) ? true : false;
                    
                    $guardian->students()->attach($studentId, [
                        'relationship_type' => $relationshipType,
                        'is_primary' => $isPrimary,
                    ]);
                }
            }

            DB::commit();
            
            $message = 'تم تحديث بيانات ولي الأمر بنجاح';
            if ($guardian->phone !== $request->phone) {
                $message .= '. كود الدخول الجديد: ' . $accessCode;
            }
            
            return redirect()->route('guardians.index')->with('success', $message);
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث ولي الأمر: ' . $e->getMessage());
        }
    }

    /**
     * حذف ولي أمر
     */
    public function destroy(Guardian $guardian)
    {
        try {
            // حذف العلاقات أولاً
            $guardian->students()->detach();
            
            // حذف ولي الأمر
            $guardian->delete();
            
            return redirect()->route('guardians.index')
                           ->with('success', 'تم حذف ولي الأمر بنجاح');
                           
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف ولي الأمر: ' . $e->getMessage());
        }
    }

    /**
     * تبديل حالة النشاط
     */
    public function toggleStatus(Guardian $guardian)
    {
        $guardian->update([
            'is_active' => !$guardian->is_active
        ]);

        $status = $guardian->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';
        
        return redirect()->route('guardians.index')
                       ->with('success', $status . ' ولي الأمر بنجاح');
    }

    /**
     * إعادة تعيين كود الدخول
     */
    public function resetAccessCode(Guardian $guardian)
    {
        $newCode = Guardian::generateAccessCode($guardian->phone);
        $guardian->update(['access_code' => $newCode]);
        
        return redirect()->route('guardians.show', $guardian)
                       ->with('success', 'تم إعادة تعيين كود الدخول: ' . $newCode);
    }

    /**
     * البحث السريع عن أولياء الأمور (AJAX)
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        $guardians = Guardian::search($search)
                           ->active()
                           ->limit(10)
                           ->get(['id', 'name', 'phone', 'relationship']);
        
        return response()->json($guardians);
    }
}
