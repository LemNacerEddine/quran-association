<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\ClassSchedule;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = ClassSchedule::with(['circle', 'circle.teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        $circles = Circle::with('teacher')->get();
        $teachers = \App\Models\Teacher::active()->get();

        return view('schedules.index_responsive', compact('schedules', 'circles', 'teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $circles = Circle::with('teacher')->get();
        return view('schedules.create', compact('circles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validatedData = $request->validate([
            'schedule_name' => 'required|string|max:255',
            'circle_id' => 'required|exists:circles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'auto_create_sessions' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        // تحويل القيم المنطقية
        $validatedData['auto_create_sessions'] = $request->has('auto_create_sessions') ? true : false;

        try {
            DB::beginTransaction();

            // الحصول على بيانات الحلقة
            $circle = Circle::findOrFail($validatedData['circle_id']);

            // إنشاء الجدولة الأساسية
            $schedule = ClassSchedule::create([
                'schedule_name' => $validatedData['schedule_name'],
                'circle_id' => $validatedData['circle_id'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'start_time' => $circle->start_time,
                'end_time' => $circle->end_time,
                'recurrence_type' => 'weekly', // Default to weekly based on circle days
                'location' => $validatedData['location'] ?? $circle->location,
                'max_students' => $circle->max_students,
                'is_active' => true,
                'auto_create_sessions' => $validatedData['auto_create_sessions'],
                'requires_attendance' => true,
                'status' => 'active',
                'description' => $validatedData['description'],
                'created_by' => auth()->id(),
            ]);

            // إنشاء الجلسات تلقائياً إذا كان مفعلاً
            if ($validatedData['auto_create_sessions']) {
                $this->createSessionsForSchedule($schedule, $circle);
            }

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'تم إنشاء الجدولة بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الجدولة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassSchedule $schedule)
    {
        $schedule->load(['circle', 'circle.teacher']);
        $sessions = ClassSession::where('schedule_id', $schedule->id)
            ->orderBy('session_date')
            ->paginate(10);

        return view('schedules.show', compact('schedule', 'sessions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassSchedule $schedule)
    {
        $circles = Circle::with('teacher')->get();
        return view('schedules.edit', compact('schedule', 'circles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassSchedule $schedule)
    {
        // التحقق من صحة البيانات
        $validatedData = $request->validate([
            'schedule_name' => 'required|string|max:255',
            'circle_id' => 'required|exists:circles,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'max_students' => 'nullable|integer|min:1|max:50',
            'is_active' => 'nullable|boolean',
            'auto_create_sessions' => 'nullable|boolean',
            'requires_attendance' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        // تحويل القيم المنطقية
        $validatedData['is_active'] = $request->has('is_active') ? true : false;
        $validatedData['auto_create_sessions'] = $request->has('auto_create_sessions') ? true : false;
        $validatedData['requires_attendance'] = $request->has('requires_attendance') ? true : false;

        try {
            DB::beginTransaction();

            // الحصول على بيانات الحلقة
            $circle = Circle::findOrFail($validatedData['circle_id']);

            // تحديث الجدولة
            $schedule->update([
                'schedule_name' => $validatedData['schedule_name'],
                'circle_id' => $validatedData['circle_id'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'start_time' => $circle->start_time,
                'end_time' => $circle->end_time,
                'location' => $validatedData['location'] ?? $circle->location,
                'max_students' => $validatedData['max_students'] ?? $circle->max_students,
                'is_active' => $validatedData['is_active'],
                'auto_create_sessions' => $validatedData['auto_create_sessions'],
                'requires_attendance' => $validatedData['requires_attendance'],
                'description' => $validatedData['description'],
            ]);

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'تم تحديث الجدولة بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الجدولة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassSchedule $schedule)
    {
        try {
            DB::beginTransaction();

            // حذف الجلسات المرتبطة أولاً
            ClassSession::where('schedule_id', $schedule->id)->delete();
            
            // حذف الجدولة
            $schedule->delete();

            DB::commit();

            return redirect()->route('schedules.index')
                ->with('success', 'تم حذف الجدولة بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الجدولة: ' . $e->getMessage());
        }
    }

    /**
     * إنشاء الجلسات بناءً على نمط التكرار
     */
    private function createSessionsForSchedule(ClassSchedule $schedule, Circle $circle)
    {
        $startDate = Carbon::parse($schedule->start_date);
        $endDate = Carbon::parse($schedule->end_date);
        $currentDate = $startDate->copy();

        // تحديد أيام الأسبوع للحلقة
        $circleDays = $this->parseCircleDays($circle->schedule_days);

        $sessions = [];

        while ($currentDate->lte($endDate)) {
            // التحقق من أن اليوم الحالي من أيام الحلقة
            if (in_array($currentDate->dayOfWeek, $circleDays)) {
                $sessions[] = [
                    'schedule_id' => $schedule->id,
                    'circle_id' => $schedule->circle_id,
                    'teacher_id' => $circle->teacher_id,
                    'session_title' => 'جلسة ' . $circle->name,
                    'session_date' => $currentDate->format('Y-m-d'),
                    'actual_start_time' => $circle->start_time,
                    'actual_end_time' => $circle->end_time,
                    'status' => 'scheduled',
                    'attendance_taken' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $currentDate->addDay();
        }

        // إدراج الجلسات بشكل مجمع للأداء
        if (!empty($sessions)) {
            ClassSession::insert($sessions);
        }

        return count($sessions);
    }

    /**
     * تحليل أيام الحلقة وتحويلها لأرقام
     */
    private function parseCircleDays($daysString)
    {
        if (empty($daysString)) {
            return [0]; // الأحد افتراضياً
        }

        $dayMap = [
            'الأحد' => 0,
            'الاثنين' => 1,
            'الثلاثاء' => 2,
            'الأربعاء' => 3,
            'الخميس' => 4,
            'الجمعة' => 5,
            'السبت' => 6,
        ];

        $days = [];
        $dayNames = explode(',', $daysString);

        foreach ($dayNames as $dayName) {
            $dayName = trim($dayName);
            if (isset($dayMap[$dayName])) {
                $days[] = $dayMap[$dayName];
            }
        }

        return empty($days) ? [0] : $days;
    }

    /**
     * فحص التعارضات
     */
    public function checkConflicts(Request $request)
    {
        $circleId = $request->input('circle_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $location = $request->input('location');

        $conflicts = [];

        // فحص تعارض الوقت
        $timeConflicts = ClassSchedule::where('circle_id', $circleId)
            ->where('is_active', true)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->get();

        if ($timeConflicts->count() > 0) {
            $conflicts[] = [
                'type' => 'time',
                'message' => 'يوجد تعارض في الوقت مع جدولات أخرى',
                'schedules' => $timeConflicts
            ];
        }

        // فحص تعارض القاعة
        if ($location) {
            $locationConflicts = ClassSchedule::where('location', $location)
                ->where('is_active', true)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                              ->where('end_date', '>=', $endDate);
                        });
                })
                ->get();

            if ($locationConflicts->count() > 0) {
                $conflicts[] = [
                    'type' => 'location',
                    'message' => 'القاعة محجوزة في هذه الفترة',
                    'schedules' => $locationConflicts
                ];
            }
        }

        return response()->json([
            'has_conflicts' => !empty($conflicts),
            'conflicts' => $conflicts
        ]);
    }

    /**
     * الحصول على القاعات المتاحة
     */
    public function getAvailableRooms(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $allRooms = [
            'المسجد الكبير - القاعة الأولى',
            'المسجد الكبير - القاعة الثانية',
            'المسجد الكبير - قاعة الحفظ',
            'المسجد الكبير - قاعة التجويد',
            'المسجد الكبير - القاعة الخارجية'
        ];

        $bookedRooms = ClassSchedule::where('is_active', true)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->pluck('location')
            ->filter()
            ->unique()
            ->toArray();

        $availableRooms = array_diff($allRooms, $bookedRooms);

        return response()->json([
            'available_rooms' => array_values($availableRooms),
            'booked_rooms' => $bookedRooms
        ]);
    }

    /**
     * Display weekly schedule view
     */
    public function weekly()
    {
        $schedules = ClassSchedule::with(['circle', 'circle.teacher'])
            ->where('is_active', true)
            ->get();
            
        return view('schedules.weekly', compact('schedules'));
    }

    /**
     * Display monthly schedule view
     */
    public function monthly()
    {
        $schedules = ClassSchedule::with(['circle', 'circle.teacher'])
            ->where('is_active', true)
            ->get();
            
        return view('schedules.monthly', compact('schedules'));
    }

    /**
     * Toggle schedule status
     */
    public function toggleStatus(ClassSchedule $schedule)
    {
        $schedule->update([
            'is_active' => !$schedule->is_active
        ]);

        return redirect()->route('schedules.index')->with('success', 'تم تحديث حالة الجدولة بنجاح');
    }

    /**
     * Handle bulk actions on schedules
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $scheduleIds = $request->input('schedule_ids', []);

        if (empty($scheduleIds)) {
            return redirect()->route('schedules.index')->with('error', 'يرجى اختيار جدولة واحدة على الأقل');
        }

        switch ($action) {
            case 'activate':
                ClassSchedule::whereIn('id', $scheduleIds)->update(['is_active' => true]);
                $message = 'تم تفعيل الجدولات المحددة بنجاح';
                break;
            case 'deactivate':
                ClassSchedule::whereIn('id', $scheduleIds)->update(['is_active' => false]);
                $message = 'تم إلغاء تفعيل الجدولات المحددة بنجاح';
                break;
            case 'delete':
                ClassSchedule::whereIn('id', $scheduleIds)->delete();
                $message = 'تم حذف الجدولات المحددة بنجاح';
                break;
            default:
                return redirect()->route('schedules.index')->with('error', 'إجراء غير صالح');
        }

        return redirect()->route('schedules.index')->with('success', $message);
    }

    /**
     * تحديد فترة اليوم بناءً على الوقت
     */
    private function getTimeOfDay($time)
    {
        $hour = (int) date('H', strtotime($time));
        
        if ($hour >= 5 && $hour < 12) {
            return 'morning';
        } elseif ($hour >= 12 && $hour < 17) {
            return 'afternoon';
        } else {
            return 'evening';
        }
    }

    /**
     * إنشاء جلسات تلقائياً للجدولة
     */
    public function createSessionsAuto(Request $request, ClassSchedule $schedule)
    {
        try {
            DB::beginTransaction();
            
            if (!$schedule->circle) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحلقة غير موجودة'
                ]);
            }
            
            $circle = $schedule->circle;
            
            // التحقق من اكتمال معلومات الحلقة
            if (!$circle->schedule_days || !$circle->start_time || !$circle->end_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'معلومات الحلقة غير مكتملة (الأيام أو التوقيت)'
                ]);
            }
            
            $sessionsCreated = $this->createSessionsForSchedule($schedule, $circle);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "تم إنشاء {$sessionsCreated} جلسة بنجاح",
                'sessions_count' => $sessionsCreated
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الجلسات: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * حذف وإعادة إنشاء جلسات الجدولة
     */
    public function recreateSessions(Request $request, ClassSchedule $schedule)
    {
        try {
            DB::beginTransaction();
            
            if (!$schedule->circle) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحلقة غير موجودة'
                ]);
            }
            
            $circle = $schedule->circle;
            
            // التحقق من اكتمال معلومات الحلقة
            if (!$circle->schedule_days || !$circle->start_time || !$circle->end_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'معلومات الحلقة غير مكتملة (الأيام أو التوقيت)'
                ]);
            }
            
            // حذف الجلسات الموجودة للجدولة (فقط التي لم يُسجل فيها حضور)
            $deletedSessions = ClassSession::where('schedule_id', $schedule->id)
                ->whereDoesntHave('attendanceSessions')
                ->delete();
            
            // إنشاء جلسات جديدة
            $sessionsCreated = $this->createSessionsForSchedule($schedule, $circle);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "تم حذف {$deletedSessions} جلسة وإنشاء {$sessionsCreated} جلسة جديدة",
                'deleted_count' => $deletedSessions,
                'created_count' => $sessionsCreated
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعادة إنشاء الجلسات: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create sessions for a specific schedule (Legacy method - kept for compatibility)
     */
    public function createSessions(Request $request, ClassSchedule $schedule)
    {
        try {
            $type = $request->input('type', 'weekly'); // weekly or monthly
            $sessionsCreated = 0;
            
            if (!$schedule->circle) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحلقة غير موجودة'
                ]);
            }
            
            $circle = $schedule->circle;
            
            if (!$circle->schedule_days || !$circle->start_time || !$circle->end_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'معلومات الحلقة غير مكتملة (الأيام أو التوقيت)'
                ]);
            }
            
            $days = explode(',', $circle->days);
            $startDate = Carbon::parse($schedule->start_date);
            $endDate = Carbon::parse($schedule->end_date);
            
            // Day mapping for Arabic days
            $dayMapping = [
                'أحد' => 0, 'الأحد' => 0, 'sunday' => 0,
                'اثنين' => 1, 'الاثنين' => 1, 'monday' => 1,
                'ثلاثاء' => 2, 'الثلاثاء' => 2, 'tuesday' => 2,
                'أربعاء' => 3, 'الأربعاء' => 3, 'wednesday' => 3,
                'خميس' => 4, 'الخميس' => 4, 'thursday' => 4,
                'جمعة' => 5, 'الجمعة' => 5, 'friday' => 5,
                'سبت' => 6, 'السبت' => 6, 'saturday' => 6
            ];
            
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                foreach ($days as $day) {
                    $dayName = trim($day);
                    
                    if (isset($dayMapping[$dayName])) {
                        $dayOfWeek = $dayMapping[$dayName];
                        
                        // Find the next occurrence of this day
                        $sessionDate = $currentDate->copy();
                        while ($sessionDate->dayOfWeek !== $dayOfWeek) {
                            $sessionDate->addDay();
                        }
                        
                        // Only create if within the schedule period
                        if ($sessionDate->gte($startDate) && $sessionDate->lte($endDate)) {
                            // Check if session already exists
                            $existingSession = ClassSession::where('circle_id', $circle->id)
                                ->where('schedule_id', $schedule->id)
                                ->where('session_date', $sessionDate->format('Y-m-d'))
                                ->first();
                            
                            if (!$existingSession) {
                                ClassSession::create([
                                    'circle_id' => $circle->id,
                                    'schedule_id' => $schedule->id,
                                    'teacher_id' => $circle->teacher_id,
                                    'session_title' => 'جلسة ' . $circle->name . ' - ' . $sessionDate->format('Y-m-d'),
                                    'session_date' => $sessionDate->format('Y-m-d'),
                                    'actual_start_time' => $circle->start_time,
                                    'actual_end_time' => $circle->end_time,
                                    'status' => 'scheduled',
                                    'session_notes' => 'جلسة تم إنشاؤها تلقائياً من الجدولة',
                                    'attendance_taken' => false
                                ]);
                                
                                $sessionsCreated++;
                            }
                        }
                    }
                }
                
                // Move to next week or month based on type
                if ($type === 'monthly') {
                    $currentDate->addMonth();
                } else {
                    $currentDate->addWeek();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "تم إنشاء {$sessionsCreated} جلسة بنجاح",
                'sessions_created' => $sessionsCreated
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الجلسات: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create weekly sessions for all active circles
     */
    public function createWeeklySessions()
    {
        try {
            $circles = Circle::where('is_active', true)->with('teacher')->get();
            $sessionsCreated = 0;
            
            foreach ($circles as $circle) {
                if (!$circle->schedule_days || !$circle->start_time || !$circle->end_time) {
                    continue;
                }
                
                $days = explode(',', $circle->days);
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                
                foreach ($days as $day) {
                    $dayName = trim($day);
                    $dayMapping = [
                        'أحد' => 0, 'الأحد' => 0, 'sunday' => 0,
                        'اثنين' => 1, 'الاثنين' => 1, 'monday' => 1,
                        'ثلاثاء' => 2, 'الثلاثاء' => 2, 'tuesday' => 2,
                        'أربعاء' => 3, 'الأربعاء' => 3, 'wednesday' => 3,
                        'خميس' => 4, 'الخميس' => 4, 'thursday' => 4,
                        'جمعة' => 5, 'الجمعة' => 5, 'friday' => 5,
                        'سبت' => 6, 'السبت' => 6, 'saturday' => 6,
                        'يومي' => 'daily' // للحلقات اليومية
                    ];
                    
                    if ($dayName === 'يومي') {
                        // إنشاء جلسات يومية للأسبوع الحالي
                        for ($i = 0; $i < 7; $i++) {
                            $sessionDate = $startDate->copy()->addDays($i);
                            
                            // Check if session already exists
                            $existingSession = ClassSession::where('circle_id', $circle->id)
                                ->whereDate('session_date', $sessionDate)
                                ->first();
                                
                            if (!$existingSession) {
                                // إنشاء أو العثور على جدولة تلقائية للحلقة
                                $schedule = ClassSchedule::firstOrCreate([
                                    'circle_id' => $circle->id,
                                    'schedule_name' => 'جدولة تلقائية - ' . $circle->name,
                                    'start_date' => $startDate->format('Y-m-d'),
                                    'end_date' => $endDate->format('Y-m-d'),
                                    'is_active' => true,
                                    'auto_create_sessions' => true
                                ], [
                                    'start_time' => $circle->start_time,
                                    'end_time' => $circle->end_time,
                                    'location' => $circle->location,
                                    'recurrence_type' => 'weekly',
                                    'status' => 'active',
                                    'created_by' => auth()->id()
                                ]);
                                
                                ClassSession::create([
                                    'schedule_id' => $schedule->id,
                                    'circle_id' => $circle->id,
                                    'teacher_id' => $circle->teacher_id,
                                    'session_title' => 'جلسة يومية - ' . $circle->name,
                                    'session_date' => $sessionDate,
                                    'status' => 'scheduled'
                                ]);
                                $sessionsCreated++;
                            }
                        }
                    } elseif (isset($dayMapping[$dayName])) {
                        $sessionDate = $startDate->copy()->addDays($dayMapping[$dayName]);
                        
                        // Check if session already exists
                        $existingSession = ClassSession::where('circle_id', $circle->id)
                            ->whereDate('session_date', $sessionDate)
                            ->first();
                            
                        if (!$existingSession) {
                            // إنشاء أو العثور على جدولة تلقائية للحلقة
                            $schedule = ClassSchedule::firstOrCreate([
                                'circle_id' => $circle->id,
                                'schedule_name' => 'جدولة تلقائية - ' . $circle->name,
                                'start_date' => $startDate->format('Y-m-d'),
                                'end_date' => $endDate->format('Y-m-d'),
                                'is_active' => true,
                                'auto_create_sessions' => true
                            ], [
                                'start_time' => $circle->start_time,
                                'end_time' => $circle->end_time,
                                'location' => $circle->location,
                                'recurrence_type' => 'weekly',
                                'status' => 'active',
                                'created_by' => auth()->id()
                            ]);
                            
                            ClassSession::create([
                                'schedule_id' => $schedule->id,
                                'circle_id' => $circle->id,
                                'teacher_id' => $circle->teacher_id,
                                'session_title' => 'جلسة أسبوعية - ' . $circle->name,
                                'session_date' => $sessionDate,
                                'status' => 'scheduled'
                            ]);
                            $sessionsCreated++;
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "تم إنشاء {$sessionsCreated} جلسة أسبوعية بنجاح"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الجلسات: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Create monthly sessions for all active circles
     */
    public function createMonthlySessions()
    {
        try {
            $circles = Circle::where('is_active', true)->with('teacher')->get();
            $sessionsCreated = 0;
            
            foreach ($circles as $circle) {
                if (!$circle->schedule_days || !$circle->start_time || !$circle->end_time) {
                    continue;
                }
                
                $days = explode(',', $circle->days);
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                
                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    foreach ($days as $day) {
                        $dayName = trim($day);
                        $dayMapping = [
                            'أحد' => 0, 'الأحد' => 0, 'sunday' => 0,
                            'اثنين' => 1, 'الاثنين' => 1, 'monday' => 1,
                            'ثلاثاء' => 2, 'الثلاثاء' => 2, 'tuesday' => 2,
                            'أربعاء' => 3, 'الأربعاء' => 3, 'wednesday' => 3,
                            'خميس' => 4, 'الخميس' => 4, 'thursday' => 4,
                            'جمعة' => 5, 'الجمعة' => 5, 'friday' => 5,
                            'سبت' => 6, 'السبت' => 6, 'saturday' => 6
                        ];
                        
                        if (isset($dayMapping[$dayName])) {
                            $weekStart = $currentDate->copy()->startOfWeek();
                            $sessionDate = $weekStart->addDays($dayMapping[$dayName]);
                            
                            if ($sessionDate >= $startDate && $sessionDate <= $endDate) {
                                // Check if session already exists
                                $existingSession = ClassSession::where('circle_id', $circle->id)
                                    ->whereDate('session_date', $sessionDate)
                                    ->first();
                                    
                                if (!$existingSession) {
                                    // إنشاء أو العثور على جدولة تلقائية للحلقة
                                    $schedule = ClassSchedule::firstOrCreate([
                                        'circle_id' => $circle->id,
                                        'schedule_name' => 'جدولة تلقائية شهرية - ' . $circle->name,
                                        'start_date' => $startDate->format('Y-m-d'),
                                        'end_date' => $endDate->format('Y-m-d'),
                                        'is_active' => true,
                                        'auto_create_sessions' => true
                                    ], [
                                        'start_time' => $circle->start_time,
                                        'end_time' => $circle->end_time,
                                        'location' => $circle->location,
                                        'recurrence_type' => 'monthly',
                                        'status' => 'active',
                                        'created_by' => auth()->id()
                                    ]);
                                    
                                    ClassSession::create([
                                        'schedule_id' => $schedule->id,
                                        'circle_id' => $circle->id,
                                        'teacher_id' => $circle->teacher_id,
                                        'session_title' => 'جلسة شهرية - ' . $circle->name,
                                        'session_date' => $sessionDate,
                                        'status' => 'scheduled'
                                    ]);
                                    $sessionsCreated++;
                                }
                            }
                        }
                    }
                    $currentDate->addWeek();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => "تم إنشاء {$sessionsCreated} جلسة شهرية بنجاح"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الجلسات: ' . $e->getMessage()
            ]);
        }
    }


}

