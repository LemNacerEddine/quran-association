@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">لوحة التحكم</h1>
                <p class="page-subtitle">نظرة عامة على نشاط الجمعية</p>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">إجمالي الطلاب</h4>
                            <h2 class="mb-0">{{ $totalStudents ?? 0 }}</h2>
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
                            <h4 class="card-title">إجمالي المعلمين</h4>
                            <h2 class="mb-0">{{ $totalTeachers ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
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
                            <h4 class="card-title">إجمالي الحلقات</h4>
                            <h2 class="mb-0">{{ $totalCircles ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-circle fa-2x"></i>
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
                            <h4 class="card-title">أولياء الأمور</h4>
                            <h2 class="mb-0">{{ $totalGuardians ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-friends fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الجلسات المحدثة -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">جلسات مكتملة</h5>
                            <h3 class="mb-0">{{ $stats['completed_sessions'] ?? 0 }}</h3>
                            <small class="opacity-75">تم تسجيل الحضور</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">جلسات فائتة</h5>
                            <h3 class="mb-0">{{ $stats['missed_sessions'] ?? 0 }}</h3>
                            <small class="opacity-75">تحتاج تسجيل حضور</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">جلسات مجدولة</h5>
                            <h3 class="mb-0">{{ $stats['scheduled_sessions'] ?? 0 }}</h3>
                            <small class="opacity-75">قادمة</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">إجمالي الجلسات</h5>
                            <h3 class="mb-0">{{ $stats['total_sessions'] ?? 0 }}</h3>
                            <small class="opacity-75">جميع الجلسات</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-list fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات الحضور -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">معدل الحضور العام</h5>
                            <h3 class="mb-0">{{ $stats['overall_attendance_rate'] ?? 0 }}%</h3>
                            <small class="opacity-75">جميع الجلسات</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">معدل الحضور الشهري</h5>
                            <h3 class="mb-0">{{ $stats['monthly_attendance_rate'] ?? 0 }}%</h3>
                            <small class="opacity-75">هذا الشهر</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-month fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card bg-gradient-dark text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">معدل الحضور الأسبوعي</h5>
                            <h3 class="mb-0">{{ $stats['weekly_attendance_rate'] ?? 0 }}%</h3>
                            <small class="opacity-75">هذا الأسبوع</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-week fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات تفصيلية لأولياء الأمور -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">أولياء أمور نشطين</h5>
                            <h3 class="mb-0">{{ $activeGuardians ?? 0 }}</h3>
                            <small class="opacity-75">من إجمالي {{ $totalGuardians ?? 0 }}</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">الآباء</h5>
                            <h3 class="mb-0">{{ $fatherGuardians ?? 0 }}</h3>
                            <small class="opacity-75">ولي أمر</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-male fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">الأمهات</h5>
                            <h3 class="mb-0">{{ $motherGuardians ?? 0 }}</h3>
                            <small class="opacity-75">ولية أمر</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-female fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-gradient-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">جدد هذا الشهر</h5>
                            <h3 class="mb-0">{{ $newGuardiansThisMonth ?? 0 }}</h3>
                            <small class="opacity-75">ولي أمر جديد</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-plus fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الإجراءات السريعة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">الإجراءات السريعة</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('students.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-user-plus mb-2"></i><br>
                                إدارة الطلاب
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('teachers.index') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-chalkboard-teacher mb-2"></i><br>
                                إدارة المعلمين
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('circles.index') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-circle mb-2"></i><br>
                                إدارة الحلقات
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('guardians.index') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-user-friends mb-2"></i><br>
                                إدارة أولياء الأمور
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="{{ route('schedules.index') }}" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-calendar mb-2"></i><br>
                                إدارة الجدولة
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                            <a href="#" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-chart-bar mb-2"></i><br>
                                التقارير
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- الإحصائيات المفيدة والحقيقية -->
    <div class="row mb-4">
        <!-- أفضل 5 طلاب في الحفظ هذا الأسبوع -->
        <div class="col-lg-6 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-star"></i>
                        أفضل 5 طلاب في الحفظ هذا الأسبوع
                    </h4>
                </div>
                <div class="card-body">
                    @if($topMemorizationStudents && $topMemorizationStudents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topMemorizationStudents as $index => $student)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge badge-warning me-2">{{ $index + 1 }}</span>
                                            {{ $student->name }}
                                        </h6>
                                        <small class="text-muted">{{ $student->circle_name ?? 'غير محدد' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-success">{{ $student->avg_memorization_rounded ?? 0 }} نقطة</span>
                                        <br>
                                        <small class="text-muted">{{ $student->sessions_count ?? 0 }} جلسة</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>لا توجد بيانات حفظ لهذا الأسبوع</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- أفضل 5 طلاب في الحضور -->
        <div class="col-lg-6 mb-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-check-circle"></i>
                        أفضل 5 طلاب في الالتزام بالحضور
                    </h4>
                </div>
                <div class="card-body">
                    @if($topAttendanceStudents && $topAttendanceStudents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topAttendanceStudents as $index => $student)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge badge-success me-2">{{ $index + 1 }}</span>
                                            {{ $student->name }}
                                        </h6>
                                        <small class="text-muted">{{ $student->circle_name ?? 'غير محدد' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-primary">{{ $student->attendance_percentage ?? 0 }}%</span>
                                        <br>
                                        <small class="text-muted">{{ $student->total_sessions ?? 0 }} جلسة</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>لا توجد بيانات حضور كافية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الطلاب المحتاجون لمتابعة -->
        <div class="col-lg-6 mb-4">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        الطلاب المحتاجون لمتابعة
                    </h4>
                </div>
                <div class="card-body">
                    @if($studentsNeedingImprovement && $studentsNeedingImprovement->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($studentsNeedingImprovement->take(5) as $student)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $student->name }}</h6>
                                        <small class="text-muted">
                                            حضور: {{ $student->attendance_percentage ?? 0 }}% | 
                                            متوسط النقاط: {{ $student->avg_points ?? 0 }}
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
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-thumbs-up fa-2x mb-2"></i>
                            <p>جميع الطلاب يؤدون بشكل جيد!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- الطلاب كثيرو التغيب -->
        <div class="col-lg-6 mb-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-times"></i>
                        الطلاب كثيرو التغيب
                    </h4>
                </div>
                <div class="card-body">
                    @if($frequentAbsentStudents && $frequentAbsentStudents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($frequentAbsentStudents->take(5) as $student)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $student->name }}</h6>
                                        <small class="text-muted">
                                            {{ $student->circle_name ?? 'غير محدد' }} | 
                                            {{ $student->teacher_name ?? 'غير محدد' }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-danger">{{ $student->absence_percentage ?? 0 }}% غياب</span>
                                        <br>
                                        <small class="text-muted">{{ $student->absent_count ?? 0 }} من {{ $student->total_sessions ?? 0 }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p>لا يوجد طلاب كثيرو التغيب</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- أفضل الحلقات أداءً -->
        <div class="col-lg-6 mb-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-trophy"></i>
                        أفضل الحلقات أداءً
                    </h4>
                </div>
                <div class="card-body">
                    @if($topPerformingCircles && $topPerformingCircles->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topPerformingCircles->take(5) as $index => $circle)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge badge-info me-2">{{ $index + 1 }}</span>
                                            {{ $circle->name }}
                                        </h6>
                                        <small class="text-muted">{{ $circle->teacher_name ?? 'غير محدد' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-success">{{ $circle->avg_points ?? 0 }} نقطة</span>
                                        <br>
                                        <small class="text-muted">{{ $circle->attendance_percentage ?? 0 }}% حضور</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>لا توجد بيانات كافية للحلقات</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- أفضل المعلمين أداءً -->
        <div class="col-lg-6 mb-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-medal"></i>
                        أفضل المعلمين أداءً
                    </h4>
                </div>
                <div class="card-body">
                    @if($topPerformingTeachers && $topPerformingTeachers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topPerformingTeachers->take(5) as $index => $teacher)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge badge-primary me-2">{{ $index + 1 }}</span>
                                            {{ $teacher->name }}
                                        </h6>
                                        <small class="text-muted">{{ $teacher->circles_count ?? 0 }} حلقة | {{ $teacher->active_students ?? 0 }} طالب</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-success">{{ $teacher->avg_points ?? 0 }} نقطة</span>
                                        <br>
                                        <small class="text-muted">{{ $teacher->attendance_percentage ?? 0 }}% حضور</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>لا توجد بيانات كافية للمعلمين</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- النشاط الأخير -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">جلسات اليوم</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الحلقة</th>
                                    <th>المعلم</th>
                                    <th>الوقت</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>حلقة الفجر للمبتدئين</td>
                                    <td>أحمد محمد الأحمد</td>
                                    <td>06:00 - 07:00</td>
                                    <td><span class="badge badge-warning">مجدولة</span></td>
                                    <td>
                                        <a href="/attendance/session/1" class="btn btn-sm btn-primary">تسجيل الحضور</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>حلقة المغرب للمتوسطين</td>
                                    <td>محمد علي السالم</td>
                                    <td>19:30 - 20:30</td>
                                    <td><span class="badge badge-success">مكتملة</span></td>
                                    <td>
                                        <a href="/sessions/2" class="btn btn-sm btn-info">عرض التفاصيل</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إحصائيات الحضور</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>معدل الحضور العام</span>
                            <span class="font-weight-bold">85%</span>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-success" style="width: 85%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>حضور هذا الأسبوع</span>
                            <span class="font-weight-bold">78%</span>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-info" style="width: 78%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>حضور اليوم</span>
                            <span class="font-weight-bold">92%</span>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-primary" style="width: 92%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

