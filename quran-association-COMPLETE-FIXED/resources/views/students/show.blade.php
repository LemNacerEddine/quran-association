@extends('layouts.dashboard')

@section('title', 'تفاصيل الطالب - ' . $student->name)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">
                <i class="fas fa-user-graduate me-2"></i>
                تفاصيل الطالب
            </h2>
            <p class="text-muted mb-0">عرض معلومات وإحصائيات الطالب</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                تعديل
            </a>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>
                العودة
            </a>
        </div>
    </div>

    <!-- Student Basic Information -->
    <div class="row mb-4">
        <!-- Student Basic Info -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-id-card me-2"></i>
                        المعلومات الأساسية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title rounded-circle bg-primary text-white" style="width: 80px; height: 80px; line-height: 80px; font-size: 2rem;">
                                {{ substr($student->name, 0, 1) }}
                            </div>
                        </div>
                        <h4 class="mb-1">{{ $student->name }}</h4>
                        <span class="badge {{ $student->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $student->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>

                    <div class="info-list">
                        <div class="info-item d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">
                                <i class="fas fa-phone me-2"></i>
                                رقم الهاتف
                            </span>
                            <span class="fw-bold">{{ $student->phone ?? 'غير محدد' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">
                                <i class="fas fa-birthday-cake me-2"></i>
                                العمر
                            </span>
                            <span class="fw-bold">{{ $student->age ?? 'غير محدد' }} سنة</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">
                                <i class="fas fa-venus-mars me-2"></i>
                                الجنس
                            </span>
                            <span class="fw-bold">{{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">
                                <i class="fas fa-calendar-plus me-2"></i>
                                تاريخ التسجيل
                            </span>
                            <span class="fw-bold">{{ $student->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Circles -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-users me-2"></i>
                        الحلقات المسجل بها
                    </h6>
                </div>
                <div class="card-body">
                    @if($student->circles && $student->circles->count() > 0)
                        @foreach($student->circles as $circle)
                            <div class="card mb-3 border-left-success">
                                <div class="card-body p-3">
                                    <h6 class="card-title mb-2">{{ $circle->name }}</h6>
                                    @if($circle->description)
                                        <p class="card-text text-muted small mb-2">{{ $circle->description }}</p>
                                    @endif
                                    @if($circle->teacher)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                                            <small class="text-muted">{{ $circle->teacher->name }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">لم يتم تسجيل الطالب في أي حلقة</h6>
                            <p class="text-muted small">يمكنك تسجيله في حلقة من خلال تعديل بياناته</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Student Performance Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-line me-2"></i>
                        ملخص الأداء
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($student->summary))
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-bottom pb-2">
                                    <h4 class="text-primary mb-0">{{ $student->summary->total_points ?? 0 }}</h4>
                                    <small class="text-muted">إجمالي النقاط</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border-bottom pb-2">
                                    <h4 class="text-success mb-0">{{ number_format($student->summary->attendance_percentage ?? 0, 1) }}%</h4>
                                    <small class="text-muted">معدل الحضور</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border-bottom pb-2">
                                    <h4 class="text-info mb-0">{{ $student->summary->avg_points ?? 0 }}</h4>
                                    <small class="text-muted">متوسط النقاط</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="border-bottom pb-2">
                                    <h4 class="text-warning mb-0">{{ $student->summary->total_sessions ?? 0 }}</h4>
                                    <small class="text-muted">عدد الجلسات</small>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bars -->
                        <div class="mt-3">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>معدل الحضور</small>
                                    <small>{{ number_format($student->summary->attendance_percentage ?? 0, 1) }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $attendanceRate = $student->summary->attendance_percentage ?? 0;
                                        $progressClass = $attendanceRate >= 80 ? 'bg-success' : ($attendanceRate >= 60 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="progress-bar {{ $progressClass }}" style="width: {{ $attendanceRate }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>متوسط النقاط</small>
                                    <small>{{ $student->summary->avg_points ?? 0 }}/10</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $avgPoints = $student->summary->avg_points ?? 0;
                                        $pointsPercentage = ($avgPoints / 10) * 100;
                                        $pointsClass = $avgPoints >= 8 ? 'bg-success' : ($avgPoints >= 6 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="progress-bar {{ $pointsClass }}" style="width: {{ $pointsPercentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">لا توجد إحصائيات متاحة</h6>
                            <p class="text-muted small">سيتم عرض الإحصائيات عند توفر بيانات الحضور</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- سجل الحضور التفصيلي الشامل -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        سجل الحضور التفصيلي
                    </h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge bg-primary">{{ $student->summary->total_sessions ?? 0 }} جلسة إجمالي</span>
                        <span class="badge bg-success">{{ $student->summary->present_count ?? 0 }} حضور</span>
                        <span class="badge bg-danger">{{ $student->summary->absent_count ?? 0 }} غياب</span>
                        <span class="badge bg-warning">{{ $student->summary->late_count ?? 0 }} تأخير</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- إحصائيات سريعة -->
                    <div class="row mb-4">
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $student->summary->total_sessions ?? 0 }}</h4>
                                    <small>إجمالي الجلسات</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $student->summary->present_count ?? 0 }}</h4>
                                    <small>أيام الحضور</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $student->summary->absent_count ?? 0 }}</h4>
                                    <small>أيام الغياب</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $student->summary->late_count ?? 0 }}</h4>
                                    <small>مرات التأخير</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-star fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $student->summary->total_points ?? 0 }}</h4>
                                    <small>إجمالي النقاط</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card bg-secondary text-white h-100">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-percentage fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $student->summary->attendance_percentage ?? 0 }}%</h4>
                                    <small>نسبة الحضور</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تحليل النقاط والتوزيع -->
                    <div class="row mb-4">
                        <div class="col-lg-6 mb-3">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-trophy me-2"></i>
                                        تحليل النقاط
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-success">{{ $student->summary->avg_points ?? 0 }}</h4>
                                                <small class="text-muted">متوسط النقاط</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-info">{{ $student->summary->total_points ?? 0 }}</h4>
                                                <small class="text-muted">إجمالي النقاط</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-3">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-chart-pie me-2"></i>
                                        توزيع الحضور والغياب
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="text-success">
                                                <h5>{{ $student->summary->present_count ?? 0 }}</h5>
                                                <small>حاضر</small>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $student->summary->attendance_percentage ?? 0 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-danger">
                                                <h5>{{ $student->summary->absent_count ?? 0 }}</h5>
                                                <small>غائب</small>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-danger" style="width: {{ (($student->summary->absent_count ?? 0) / max(($student->summary->total_sessions ?? 1), 1)) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-warning">
                                                <h5>{{ $student->summary->late_count ?? 0 }}</h5>
                                                <small>متأخر</small>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-warning" style="width: {{ (($student->summary->late_count ?? 0) / max(($student->summary->total_sessions ?? 1), 1)) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- شريط التقدم نحو الهدف -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-bullseye me-2"></i>
                                التقدم نحو الهدف
                            </h6>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: {{ min(($student->summary->attendance_percentage ?? 0), 100) }}%">
                                    {{ $student->summary->attendance_percentage ?? 0 }}% نسبة الحضور
                                </div>
                            </div>
                            <small class="text-muted">الهدف: 85% نسبة حضور</small>
                        </div>
                    </div>

                    <!-- الجدول التفصيلي -->
                    @if($recentAttendances && $recentAttendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>اليوم</th>
                                        <th>الحالة</th>
                                        <th>النقاط</th>
                                        <th>وقت الوصول</th>
                                        <th>الملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendances as $attendance)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($attendance->session_date)->format('Y-m-d') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($attendance->session_date)->locale('ar')->dayName }}</td>
                                            <td>
                                                @if($attendance->status == 'present')
                                                    <span class="badge bg-success">حاضر</span>
                                                @elseif($attendance->status == 'absent')
                                                    <span class="badge bg-danger">غائب</span>
                                                @elseif($attendance->status == 'late')
                                                    <span class="badge bg-warning">متأخر</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $attendance->final_points ?? 0 }}</span>
                                            </td>
                                            <td>
                                                @if($attendance->actual_start_time)
                                                    {{ \Carbon\Carbon::parse($attendance->actual_start_time)->format('H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $attendance->notes ?? 'لا توجد ملاحظات' }}</td>
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
        </div>
    </div>
</div>
@endsection

