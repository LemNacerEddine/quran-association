@extends('layouts.app')

@section('title', 'تسجيل الحضور - جمعية تحفيظ القرآن الكريم')

@section('content')
<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary">
                    <i class="fas fa-calendar-check me-2"></i>
                    تسجيل الحضور
                </h2>
                <a href="{{ route('attendance.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus me-2"></i>
                    تسجيل حضور جديد
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Quick Attendance Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        تسجيل حضور سريع
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('attendance.create') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="circle_id" class="form-label">الحلقة</label>
                            <select name="circle_id" id="circle_id" class="form-select" required>
                                <option value="">اختر الحلقة</option>
                                @foreach($circles as $circle)
                                    <option value="{{ $circle->id }}">
                                        {{ $circle->name }} - {{ $circle->teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="form-label">التاريخ</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ $today->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-arrow-right me-2"></i>
                                متابعة
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pending Sessions (Need Attendance) -->
            @if($pendingSessions->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        جلسات تحتاج تسجيل حضور ({{ $pendingSessions->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($pendingSessions as $session)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-warning shadow-sm h-100 session-card" data-session-status="pending">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        {{ $session->circle->name }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <small class="text-muted">المعلم:</small> {{ $session->circle->teacher->name }}<br>
                                        <small class="text-muted">التاريخ:</small> {{ $session->session_date->format('d/m/Y') }}<br>
                                        <small class="text-muted">الطلاب:</small> {{ $session->circle->students->count() }}
                                    </p>
                                    <a href="{{ route('attendance.session', $session->id) }}" 
                                       class="btn btn-warning btn-sm w-100 pulse-animation">
                                        <i class="fas fa-user-check me-2"></i>
                                        تسجيل الحضور الآن
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Pending Points Sessions -->
            @if($pendingPointsSessions->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        الحصص المكتملة في انتظار إكمال النقاط ({{ $pendingPointsSessions->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($pendingPointsSessions as $session)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-warning shadow-sm h-100 session-card" data-session-status="pending-points">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-hourglass-half me-2"></i>
                                        {{ $session->circle->name }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <small class="text-muted">المعلم:</small> {{ $session->circle->teacher->name }}<br>
                                        <small class="text-muted">التاريخ:</small> {{ $session->session_date->format('d/m/Y') }}<br>
                                        <small class="text-muted">الحضور:</small> {{ $session->present_students ?? 0 }}/{{ $session->total_students ?? $session->circle->students->count() }}<br>
                                        <small class="text-muted">النسبة:</small> {{ number_format($session->attendance_percentage ?? 0, 1) }}%
                                    </p>
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('attendance.session', $session->id) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit me-1"></i>
                                            إكمال النقاط
                                        </a>
                                        <a href="{{ route('sessions.show', $session->id) }}" 
                                           class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-eye me-1"></i>
                                            عرض
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Fully Completed Sessions -->
            @if($fullyCompletedSessions->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-double me-2"></i>
                        الحصص المكتملة بالكامل ({{ $fullyCompletedSessions->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($fullyCompletedSessions as $session)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-success shadow-sm h-100 session-card" data-session-status="fully-completed">
                                <div class="card-header bg-success text-white">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-trophy me-2"></i>
                                        {{ $session->circle->name }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <small class="text-muted">المعلم:</small> {{ $session->circle->teacher->name }}<br>
                                        <small class="text-muted">التاريخ:</small> {{ $session->session_date->format('d/m/Y') }}<br>
                                        <small class="text-muted">الحضور:</small> {{ $session->present_students ?? 0 }}/{{ $session->total_students ?? $session->circle->students->count() }}<br>
                                        <small class="text-muted">النسبة:</small> {{ number_format($session->attendance_percentage ?? 0, 1) }}%<br>
                                        <small class="text-success"><strong>النقاط:</strong> {{ $session->attendances->sum('final_points') }} نقطة</small>
                                    </p>
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('attendance.session', $session->id) }}?view=readonly" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-eye me-1"></i>
                                            عرض التفاصيل
                                        </a>
                                        <a href="{{ route('sessions.show', $session->id) }}" 
                                           class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-chart-bar me-1"></i>
                                            الإحصائيات
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Archived Sessions (Browse Previous) -->
            @if($archivedSessions->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-archive me-2"></i>
                        الحصص المؤرشفة السابقة - للتصفح ({{ $archivedSessions->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($archivedSessions as $session)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-info">
                                <div class="card-body">
                                    <h6 class="card-title text-info">{{ $session->circle->name }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">المعلم:</small> {{ $session->circle->teacher->name }}<br>
                                        <small class="text-muted">التاريخ:</small> {{ $session->session_date->format('d/m/Y') }}<br>
                                        <small class="text-muted">الحضور:</small> {{ $session->present_students ?? 0 }}/{{ $session->total_students ?? $session->circle->students->count() }}<br>
                                        <small class="text-muted">النسبة:</small> {{ number_format($session->attendance_percentage ?? 0, 1) }}%
                                    </p>
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('attendance.session', $session->id) }}?view=readonly" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye me-1"></i>
                                            عرض فقط
                                        </a>
                                        <a href="{{ route('attendance.session', $session->id) }}" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-edit me-1"></i>
                                            تعديل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
/* تأثيرات بصرية للجلسات */
.session-card {
    transition: all 0.3s ease;
    border-width: 2px !important;
}

.session-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.session-card[data-session-status="pending"] {
    animation: pulse-border 2s infinite;
}

.session-card[data-session-status="completed"] {
    border-color: #28a745 !important;
}

@keyframes pulse-border {
    0% { border-color: #ffc107; box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
    50% { border-color: #ff8c00; box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
    100% { border-color: #ffc107; box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}

.pulse-animation {
    animation: pulse-button 1.5s infinite;
}

@keyframes pulse-button {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* تحسين الألوان */
.card-header.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%) !important;
}

.card-header.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

/* تأثير الانتقال السلس */
.session-card .card-body {
    transition: background-color 0.3s ease;
}

.session-card:hover .card-body {
    background-color: rgba(0,0,0,0.02);
}
</style>

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Set current time for session type
    document.addEventListener('DOMContentLoaded', function() {
        const hour = new Date().getHours();
        const sessionSelect = document.getElementById('session_type');
        
        if (hour < 12) {
            sessionSelect.value = 'morning';
        } else {
            sessionSelect.value = 'evening';
        }
    });
</script>
@endsection

