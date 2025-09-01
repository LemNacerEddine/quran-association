@extends('layouts.dashboard')

@section('title', 'إدارة الجلسات')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                        إدارة الجلسات
                    </h2>
                    <p class="text-muted mb-0">تصنيف وإدارة جلسات الحلقات حسب الحالة والوقت</p>
                </div>
                <div>
                    <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        إضافة جلسة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-alt text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">إجمالي الجلسات</h6>
                            <h4 class="mb-0 text-primary">{{ $stats['total_sessions'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">جلسات مكتملة</h6>
                            <h4 class="mb-0 text-success">{{ $stats['completed_sessions'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-day text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">جلسات اليوم</h6>
                            <h4 class="mb-0 text-info">{{ $stats['today_sessions'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-percentage text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="card-title mb-1">متوسط الحضور</h6>
                            <h4 class="mb-0 text-warning">{{ number_format($stats['average_attendance'], 1) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Week Sessions -->
    @if(count($classifiedSessions['current_week']['live']) > 0 || 
        count($classifiedSessions['current_week']['upcoming']) > 0 || 
        count($classifiedSessions['current_week']['missed']) > 0 || 
        count($classifiedSessions['current_week']['completed']) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-week me-2"></i>
                        جلسات هذا الأسبوع
                    </h5>
                </div>
                <div class="card-body p-0">
                    
                    <!-- Live Sessions -->
                    @if(count($classifiedSessions['current_week']['live']) > 0)
                    <div class="border-bottom">
                        <div class="p-3 bg-warning bg-opacity-10">
                            <h6 class="mb-3">
                                <i class="fas fa-circle text-danger blink me-2"></i>
                                الجلسات الجارية الآن
                                <span class="badge bg-warning text-dark ms-2">{{ count($classifiedSessions['current_week']['live']) }}</span>
                            </h6>
                            <div class="row">
                                @foreach($classifiedSessions['current_week']['live'] as $session)
                                    @include('sessions.partials.session_card', ['session' => $session])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Missed Sessions -->
                    @if(count($classifiedSessions['current_week']['missed']) > 0)
                    <div class="border-bottom">
                        <div class="p-3 bg-danger bg-opacity-10">
                            <h6 class="mb-3">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                الجلسات الفائتة
                                <span class="badge bg-danger ms-2">{{ count($classifiedSessions['current_week']['missed']) }}</span>
                            </h6>
                            <div class="row">
                                @foreach($classifiedSessions['current_week']['missed'] as $session)
                                    @include('sessions.partials.session_card', ['session' => $session])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Upcoming Sessions -->
                    @if(count($classifiedSessions['current_week']['upcoming']) > 0)
                    <div class="border-bottom">
                        <div class="p-3 bg-info bg-opacity-10">
                            <h6 class="mb-3">
                                <i class="fas fa-clock text-info me-2"></i>
                                الجلسات القادمة
                                <span class="badge bg-info ms-2">{{ count($classifiedSessions['current_week']['upcoming']) }}</span>
                            </h6>
                            <div class="row">
                                @foreach($classifiedSessions['current_week']['upcoming'] as $session)
                                    @include('sessions.partials.session_card', ['session' => $session])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Completed Sessions -->
                    @if(count($classifiedSessions['current_week']['completed']) > 0)
                    <div>
                        <div class="p-3 bg-success bg-opacity-10">
                            <h6 class="mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                الجلسات المكتملة
                                <span class="badge bg-success ms-2">{{ count($classifiedSessions['current_week']['completed']) }}</span>
                            </h6>
                            <div class="row">
                                @foreach($classifiedSessions['current_week']['completed'] as $session)
                                    @include('sessions.partials.session_card', ['session' => $session])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Next Week Sessions -->
    @if(count($classifiedSessions['next_week']['upcoming']) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>
                        جلسات الأسبوع القادم
                        <span class="badge bg-light text-info ms-2">{{ count($classifiedSessions['next_week']['upcoming']) }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($classifiedSessions['next_week']['upcoming'] as $session)
                            @include('sessions.partials.session_card', ['session' => $session])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Previous Weeks Sessions -->
    @if(count($classifiedSessions['previous_weeks']['completed']) > 0 || count($classifiedSessions['previous_weeks']['missed']) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        الأسابيع السابقة
                    </h5>
                </div>
                <div class="card-body p-0">
                    
                    <!-- Previous Missed Sessions -->
                    @if(count($classifiedSessions['previous_weeks']['missed']) > 0)
                    <div class="border-bottom">
                        <div class="p-3 bg-danger bg-opacity-10">
                            <h6 class="mb-3">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                الجلسات الفائتة
                                <span class="badge bg-danger ms-2">{{ count($classifiedSessions['previous_weeks']['missed']) }}</span>
                            </h6>
                            <div class="row">
                                @foreach($classifiedSessions['previous_weeks']['missed'] as $session)
                                    @include('sessions.partials.session_card', ['session' => $session])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Previous Completed Sessions -->
                    @if(count($classifiedSessions['previous_weeks']['completed']) > 0)
                    <div>
                        <div class="p-3 bg-success bg-opacity-10">
                            <h6 class="mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                الجلسات المكتملة
                                <span class="badge bg-success ms-2">{{ count($classifiedSessions['previous_weeks']['completed']) }}</span>
                            </h6>
                            <div class="row">
                                @foreach($classifiedSessions['previous_weeks']['completed'] as $session)
                                    @include('sessions.partials.session_card', ['session' => $session])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Empty State -->
    @if(empty($classifiedSessions['current_week']['live']) && 
        empty($classifiedSessions['current_week']['upcoming']) && 
        empty($classifiedSessions['current_week']['missed']) && 
        empty($classifiedSessions['current_week']['completed']) &&
        empty($classifiedSessions['next_week']['upcoming']) &&
        empty($classifiedSessions['previous_weeks']['completed']) &&
        empty($classifiedSessions['previous_weeks']['missed']))
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">لا توجد جلسات</h4>
                    <p class="text-muted mb-4">لم يتم إنشاء أي جلسات بعد. ابدأ بإضافة جلسة جديدة.</p>
                    <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        إضافة جلسة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<!-- Custom Styles -->
<style>
.blink {
    animation: blink 1s linear infinite;
}

@keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0.3; }
    100% { opacity: 1; }
}

.bg-success-light {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-info-light {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.session-card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.session-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.session-card .card-body {
    padding: 1.25rem;
}

.session-status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1;
}

.time-info {
    font-size: 0.875rem;
    font-weight: 500;
}

.attendance-progress {
    height: 6px;
    border-radius: 3px;
}

.session-actions {
    margin-top: 1rem;
}

.session-actions .btn {
    border-radius: 8px;
    font-weight: 500;
}
</style>
@endsection

