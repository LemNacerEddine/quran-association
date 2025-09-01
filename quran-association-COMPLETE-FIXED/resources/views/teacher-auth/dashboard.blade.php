@extends('layouts.teacher')

@section('title', 'لوحة تحكم المعلم')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary mb-1">مرحباً بك، {{ $teacher->name }}</h2>
                    <p class="text-muted mb-0">آخر دخول: {{ $teacher->last_login_at ? $teacher->last_login_at->format('H:i d-m-Y') : 'أول مرة' }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-success fs-6">معلم نشط</span>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['circles'] }}</h3>
                            <p class="mb-0">الحلقات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['students'] }}</h3>
                            <p class="mb-0">الطلاب</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['today_sessions'] }}</h3>
                            <p class="mb-0">جلسات اليوم</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['completed_sessions'] }}</h3>
                            <p class="mb-0">جلسات مكتملة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- جلسات اليوم -->
    @if($todaySessions->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day me-2"></i>
                        جلسات اليوم ({{ $todaySessions->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($todaySessions as $session)
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">{{ $session->circle->name }}</h6>
                                        <span class="badge bg-primary">{{ $session->status }}</span>
                                    </div>
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $session->start_time }} - {{ $session->end_time }}
                                    </p>
                                    @if($session->title)
                                    <p class="mb-2">{{ $session->title }}</p>
                                    @endif
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('teacher.sessions.show', $session) }}" class="btn btn-success">
                                            <i class="fas fa-play me-1"></i>بدء الجلسة
                                        </a>
                                        <a href="{{ route('teacher.sessions.attendance', $session) }}" class="btn btn-warning">
                                            <i class="fas fa-users me-1"></i>تسجيل الحضور
                                        </a>
                                        <a href="{{ route('teacher.sessions.show', $session) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-cog me-1"></i>إدارة
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- الحلقات والجلسات -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="sessionsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill mx-1 px-4 py-2 fw-bold shadow-sm" 
                                    style="background: linear-gradient(135deg, #007bff, #0056b3); border: none; color: white;" 
                                    id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
                                <i class="fas fa-calendar-plus me-2"></i>
                                الجلسات القادمة ({{ $upcomingSessions->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill mx-1 px-4 py-2 fw-bold shadow-sm" 
                                    style="background: linear-gradient(135deg, #6c757d, #495057); border: none; color: white;" 
                                    id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
                                <i class="fas fa-history me-2"></i>
                                الجلسات السابقة ({{ $pastSessions->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill mx-1 px-4 py-2 fw-bold shadow-sm" 
                                    style="background: linear-gradient(135deg, #28a745, #1e7e34); border: none; color: white;" 
                                    id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                                <i class="fas fa-check-circle me-2"></i>
                                الجلسات المكتملة ({{ $completedSessions->count() }})
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="sessionsTabContent">
                        <!-- الجلسات القادمة -->
                        <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                            @if($upcomingSessions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الحلقة</th>
                                            <th>التاريخ</th>
                                            <th>الوقت</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcomingSessions as $session)
                                        <tr>
                                            <td>
                                                <strong>{{ $session->circle->name }}</strong>
                                                @if($session->title)
                                                <br><small class="text-muted">{{ $session->title }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $session->session_date }}</td>
                                            <td>{{ $session->start_time }} - {{ $session->end_time }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $session->status }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="#" class="btn btn-outline-primary">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-warning">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-outline-danger">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد جلسات قادمة</p>
                            </div>
                            @endif
                        </div>

                        <!-- الجلسات السابقة -->
                        <div class="tab-pane fade" id="past" role="tabpanel">
                            @if($pastSessions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الحلقة</th>
                                            <th>التاريخ</th>
                                            <th>الوقت</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pastSessions as $session)
                                        <tr>
                                            <td>
                                                <strong>{{ $session->circle->name }}</strong>
                                                @if($session->title)
                                                <br><small class="text-muted">{{ $session->title }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $session->session_date }}</td>
                                            <td>{{ $session->start_time }} - {{ $session->end_time }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $session->status }}</span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye me-1"></i>عرض
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد جلسات سابقة</p>
                            </div>
                            @endif
                        </div>

                        <!-- الجلسات المكتملة -->
                        <div class="tab-pane fade" id="completed" role="tabpanel">
                            @if($completedSessions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الحلقة</th>
                                            <th>التاريخ</th>
                                            <th>المدة</th>
                                            <th>الحضور</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($completedSessions as $session)
                                        <tr>
                                            <td>
                                                <strong>{{ $session->circle->name }}</strong>
                                                @if($session->title)
                                                <br><small class="text-muted">{{ $session->title }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $session->session_date }}</td>
                                            <td>-</td>
                                            <td>
                                                <span class="badge bg-success">{{ $session->attendances->where('status', 'present')->count() }}</span>
                                                <span class="badge bg-danger">{{ $session->attendances->where('status', 'absent')->count() }}</span>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye me-1"></i>عرض
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد جلسات مكتملة</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

