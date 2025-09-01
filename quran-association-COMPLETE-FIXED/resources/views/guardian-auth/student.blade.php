@extends('layouts.guardian')

@section('title', 'تفاصيل الطالب - ' . $student->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Student Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">
                                <i class="fas fa-user-graduate me-2"></i>
                                {{ $student->name }}
                            </h2>
                            <p class="mb-0 opacity-75">
                                @if($student->circle)
                                    الحلقة: {{ $student->circle->name }}
                                    @if($student->circle->teacher)
                                        - المعلم: {{ $student->circle->teacher->name }}
                                    @endif
                                @else
                                    غير مسجل في حلقة حالياً
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-graduation-cap fa-4x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $attendanceStats->present_count ?? 0 }}</h3>
                            <small>أيام الحضور</small>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $attendanceStats->absent_count ?? 0 }}</h3>
                            <small>أيام الغياب</small>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $attendanceStats->total_points ?? 0 }}</h3>
                            <small>إجمالي النقاط</small>
                        </div>
                        <i class="fas fa-star fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $attendanceStats->attendance_rate ?? 0 }}%</h3>
                            <small>نسبة الحضور</small>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Student Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات الطالب
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">الاسم الكامل</small>
                        <div class="fw-bold">{{ $student->name }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">العمر</small>
                        <div class="fw-bold">{{ $student->age ?? 'غير محدد' }} سنة</div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">الجنس</small>
                        <div class="fw-bold">
                            @if($student->gender == 'male')
                                ذكر
                            @elseif($student->gender == 'female')
                                أنثى
                            @else
                                غير محدد
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">المستوى التعليمي</small>
                        <div class="fw-bold">{{ $student->education_level ?? 'غير محدد' }}</div>
                    </div>
                    
                    @if($student->phone)
                    <div class="mb-3">
                        <small class="text-muted d-block">رقم الهاتف</small>
                        <div class="fw-bold">
                            <a href="tel:{{ $student->phone }}" class="text-decoration-none">
                                {{ $student->phone }}
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    @if($student->address)
                    <div class="mb-3">
                        <small class="text-muted d-block">العنوان</small>
                        <div class="fw-bold">{{ $student->address }}</div>
                    </div>
                    @endif
                    
                    <div class="mb-0">
                        <small class="text-muted d-block">حالة التسجيل</small>
                        <span class="badge bg-{{ $student->is_active ? 'success' : 'danger' }}">
                            {{ $student->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Circle Information -->
            @if($student->circle)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-circle me-2"></i>
                        معلومات الحلقة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">اسم الحلقة</small>
                        <div class="fw-bold text-primary">{{ $student->circle->name }}</div>
                    </div>
                    
                    @if($student->circle->teacher)
                    <div class="mb-3">
                        <small class="text-muted d-block">المعلم</small>
                        <div class="fw-bold">{{ $student->circle->teacher->name }}</div>
                    </div>
                    @endif
                    
                    @if($student->circle->location)
                    <div class="mb-3">
                        <small class="text-muted d-block">المكان</small>
                        <div class="fw-bold">{{ $student->circle->location }}</div>
                    </div>
                    @endif
                    
                    @if($student->circle->level)
                    <div class="mb-3">
                        <small class="text-muted d-block">المستوى</small>
                        <div class="fw-bold">{{ $student->circle->level }}</div>
                    </div>
                    @endif
                    
                    @if($student->circle->schedule_days)
                    <div class="mb-0">
                        <small class="text-muted d-block">أيام الحلقة</small>
                        <div class="fw-bold">{{ $student->circle->schedule_days }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Recent Attendance -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        سجل الحضور الأخير
                    </h5>
                    <a href="{{ route('guardian.student.reports', $student->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-chart-line me-1"></i>
                        التقارير التفصيلية
                    </a>
                </div>
                <div class="card-body">
                    @if($recentAttendance && $recentAttendance->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                        <th>النقاط</th>
                                        <th>الملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendance as $attendance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->session_date)->format('Y-m-d') }}</td>
                                        <td>
                                            @if($attendance->status == 'present')
                                                <span class="badge bg-success">حاضر</span>
                                            @elseif($attendance->status == 'absent')
                                                <span class="badge bg-danger">غائب</span>
                                            @elseif($attendance->status == 'late')
                                                <span class="badge bg-warning">متأخر</span>
                                            @else
                                                <span class="badge bg-secondary">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $attendance->final_points ?? 0 }}</span>
                                        </td>
                                        <td>{{ $attendance->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد سجلات حضور</h5>
                            <p class="text-muted">لم يتم تسجيل أي حضور للطالب حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('guardian.student.reports', $student->id) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-chart-bar me-2"></i>
                                تقارير الحضور والغياب
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('guardian.dashboard') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left me-2"></i>
                                العودة للوحة التحكم
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

