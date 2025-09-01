@extends('layouts.dashboard')

@section('title', 'قائمة الطلاب')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-graduate text-primary me-2"></i>
            قائمة الطلاب
        </h1>
        <a href="{{ route('students.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>إضافة طالب جديد
        </a>
    </div>

    <!-- إحصائيات سريعة للطلاب -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">إجمالي الطلاب</h4>
                            <h2 class="mb-0">{{ $generalStats['total_students'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">الطلاب النشطون</h4>
                            <h2 class="mb-0">{{ $generalStats['active_students'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">متوسط الحضور</h4>
                            <h2 class="mb-0">{{ round($generalStats['avg_attendance_rate'] ?? 0, 1) }}%</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">يحتاجون متابعة</h4>
                            <h2 class="mb-0">{{ $generalStats['students_needing_attention'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ملخصات سريعة -->
    <div class="row mb-4">
        <!-- أفضل الطلاب -->
        <div class="col-lg-6 mb-3">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy"></i>
                        أفضل الطلاب أداءً
                    </h5>
                </div>
                <div class="card-body">
                    @if($topStudents && $topStudents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topStudents as $index => $student)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge badge-success me-2">{{ $index + 1 }}</span>
                                            {{ $student->name }}
                                        </h6>
                                        <small class="text-muted">{{ $student->circle_name ?? 'غير محدد' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-primary">{{ $student->total_points ?? 0 }} نقطة</span>
                                        <br>
                                        <small class="text-muted">{{ $student->attendance_percentage ?? 0 }}% حضور</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>لا توجد بيانات كافية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الطلاب المحتاجون لمتابعة -->
        <div class="col-lg-6 mb-3">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        الطلاب المحتاجون لمتابعة
                    </h5>
                </div>
                <div class="card-body">
                    @if($studentsNeedingAttention && $studentsNeedingAttention->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($studentsNeedingAttention as $student)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $student->name }}</h6>
                                        <small class="text-muted">
                                            حضور: {{ $student->attendance_percentage ?? 0 }}% | 
                                            نقاط: {{ $student->avg_points ?? 0 }}
                                        </small>
                                    </div>
                                    <div>
                                        <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                            متابعة
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-thumbs-up fa-2x mb-2"></i>
                            <p>جميع الطلاب يؤدون بشكل جيد!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="card shadow mb-4 d-none d-lg-block">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">جميع الطلاب المسجلين</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>العمر</th>
                            <th>الجنس</th>
                            <th>الحلقة</th>
                            <th>ملخص الأداء</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="avatar-initial bg-primary rounded-circle">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $student->name }}</strong>
                                        @if($student->phone)
                                            <br><small class="text-muted">{{ $student->phone }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($student->birth_date)
                                    {{ \Carbon\Carbon::parse($student->birth_date)->age }} سنة
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                @if($student->gender == 'male')
                                    <span class="badge bg-primary text-white">ذكر</span>
                                @elseif($student->gender == 'female')
                                    <span class="badge bg-danger text-white">أنثى</span>
                                @else
                                    <span class="badge bg-secondary text-white">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                @if($student->circles && $student->circles->count() > 0)
                                    <div>
                                        @foreach($student->circles as $circle)
                                            <div class="mb-1">
                                                <strong>{{ $circle->name }}</strong>
                                                @if($circle->teacher)
                                                    <br><small class="text-muted">{{ $circle->teacher->name }}</small>
                                                @endif
                                            </div>
                                            @if(!$loop->last)
                                                <hr class="my-1">
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif($student->circle)
                                    <div>
                                        <strong>{{ $student->circle->name }}</strong>
                                        @if($student->circle->teacher)
                                            <br><small class="text-muted">{{ $student->circle->teacher->name }}</small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">غير مسجل في حلقة</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($student->summary))
                                    <div class="performance-summary">
                                        <!-- نسبة الحضور -->
                                        <div class="mb-1">
                                            <small class="text-muted">الحضور:</small>
                                            <span class="badge 
                                                @if($student->summary->attendance_percentage >= 90) badge-success
                                                @elseif($student->summary->attendance_percentage >= 75) badge-warning
                                                @else badge-danger
                                                @endif">
                                                {{ $student->summary->attendance_percentage }}%
                                            </span>
                                        </div>
                                        
                                        <!-- متوسط النقاط -->
                                        <div class="mb-1">
                                            <small class="text-muted">متوسط النقاط:</small>
                                            <span class="badge 
                                                @if($student->summary->avg_points >= 8) badge-success
                                                @elseif($student->summary->avg_points >= 6) badge-warning
                                                @else badge-danger
                                                @endif">
                                                {{ $student->summary->avg_points }}
                                            </span>
                                        </div>
                                        
                                        <!-- إجمالي النقاط -->
                                        <div class="mb-1">
                                            <small class="text-muted">إجمالي النقاط:</small>
                                            <span class="badge badge-primary">{{ $student->summary->total_points }}</span>
                                        </div>
                                        
                                        <!-- عدد الجلسات -->
                                        <div class="mt-1">
                                            <small class="text-muted">{{ $student->summary->total_sessions }} جلسة</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">لا توجد بيانات</span>
                                @endif
                            </td>
                            <td>
                                @if($student->is_active)
                                    <span class="badge bg-success text-white">نشط</span>
                                @else
                                    <span class="badge bg-secondary text-white">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد طلاب مسجلين</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="d-lg-none">
        @forelse($students as $student)
        <div class="card shadow mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <div class="avatar-initial bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; color: white;">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="col-9">
                        <h5 class="card-title mb-1">{{ $student->name }}</h5>
                        @if($student->phone)
                            <p class="text-muted mb-1"><i class="fas fa-phone me-1"></i>{{ $student->phone }}</p>
                        @endif
                        <p class="text-muted mb-2"><small>رقم الطالب: {{ $student->student_id }}</small></p>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>العمر:</strong>
                    </div>
                    <div class="col-6">
                        @if($student->birth_date)
                            {{ \Carbon\Carbon::parse($student->birth_date)->age }} سنة
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>الجنس:</strong>
                    </div>
                    <div class="col-6">
                        @if($student->gender == 'male')
                            <span class="badge bg-primary text-white">ذكر</span>
                        @elseif($student->gender == 'female')
                            <span class="badge bg-danger text-white">أنثى</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>الحلقات:</strong>
                    </div>
                    <div class="col-6">
                        @if($student->circles && $student->circles->count() > 0)
                            @foreach($student->circles as $circle)
                                <span class="badge bg-success text-white mb-1">{{ $circle->name }}</span><br>
                            @endforeach
                        @else
                            <span class="badge bg-warning text-dark">غير مسجل في حلقة</span>
                        @endif
                    </div>
                </div>
                
                <!-- ملخص الأداء في Mobile View -->
                @if(isset($student->summary))
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>ملخص الأداء:</strong>
                        <div class="mt-2">
                            <!-- نسبة الحضور -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small>الحضور:</small>
                                <span class="badge 
                                    @if($student->summary->attendance_percentage >= 90) bg-success
                                    @elseif($student->summary->attendance_percentage >= 75) bg-warning
                                    @else bg-danger
                                    @endif text-white">
                                    {{ $student->summary->attendance_percentage }}%
                                </span>
                            </div>
                            
                            <!-- متوسط النقاط -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small>متوسط النقاط:</small>
                                <span class="badge 
                                    @if($student->summary->avg_points >= 8) bg-success
                                    @elseif($student->summary->avg_points >= 6) bg-warning
                                    @else bg-danger
                                    @endif text-white">
                                    {{ $student->summary->avg_points }}
                                </span>
                            </div>
                            
                            <!-- إجمالي النقاط -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small>إجمالي النقاط:</small>
                                <span class="badge bg-primary text-white">{{ $student->summary->total_points }}</span>
                            </div>
                            
                            <!-- عدد الجلسات -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small>عدد الجلسات:</small>
                                <small class="text-muted">{{ $student->summary->total_sessions }} جلسة</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>الحالة:</strong>
                    </div>
                    <div class="col-6">
                        @if($student->is_active)
                            <span class="badge bg-success text-white">نشط</span>
                        @else
                            <span class="badge bg-danger text-white">غير نشط</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>تاريخ التسجيل:</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">{{ $student->created_at->format('Y-m-d') }}</small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye me-1"></i>عرض
                    </a>
                    <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-1"></i>تعديل
                    </a>
                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                            <i class="fas fa-trash me-1"></i>حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="card shadow">
            <div class="card-body text-center">
                <p class="text-muted">لا توجد طلاب مسجلين</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

