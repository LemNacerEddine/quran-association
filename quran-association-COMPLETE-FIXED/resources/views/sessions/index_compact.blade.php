@extends('layouts.dashboard')

@section('title', 'إدارة الجلسات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">
                <i class="fas fa-calendar-alt me-2"></i>
                إدارة الجلسات
            </h2>
            <p class="text-muted mb-0">تصنيف وإدارة جلسات الحلقات حسب الحالة والوقت</p>
        </div>
        <div>
            <a href="{{ route('sessions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة جلسة جديدة
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-check text-primary fs-4"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-1">إجمالي الجلسات</h6>
                    <h4 class="text-primary mb-0">{{ $stats['total_sessions'] }}</h4>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-1">جلسات مكتملة</h6>
                    <h4 class="text-success mb-0">{{ $stats['completed_sessions'] }}</h4>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-info fs-4"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-1">جلسات اليوم</h6>
                    <h4 class="text-info mb-0">{{ $stats['today_sessions'] }}</h4>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-percentage text-warning fs-4"></i>
                        </div>
                    </div>
                    <h6 class="text-muted mb-1">متوسط الحضور</h6>
                    <h4 class="text-warning mb-0">{{ $stats['attendance_rate'] }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <nav class="nav nav-pills nav-fill">
                <a class="nav-link active" id="today-tab" data-bs-toggle="pill" href="#today" role="tab">
                    <i class="fas fa-calendar-day me-2"></i>
                    جلسات اليوم
                    <span class="badge bg-primary ms-2">{{ count($classifiedSessions['today']) }}</span>
                </a>
                <a class="nav-link" id="upcoming-tab" data-bs-toggle="pill" href="#upcoming" role="tab">
                    <i class="fas fa-clock me-2"></i>
                    الجلسات القادمة
                    <span class="badge bg-info ms-2">{{ count($classifiedSessions['upcoming']) }}</span>
                </a>
                <a class="nav-link" id="missed-tab" data-bs-toggle="pill" href="#missed" role="tab">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    الجلسات الفائتة
                    <span class="badge bg-danger ms-2">{{ count($classifiedSessions['missed']) }}</span>
                </a>
                <a class="nav-link" id="completed-tab" data-bs-toggle="pill" href="#completed" role="tab">
                    <i class="fas fa-check-circle me-2"></i>
                    الجلسات المكتملة
                    <span class="badge bg-success ms-2">{{ count($classifiedSessions['completed']) }}</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Today Sessions -->
        <div class="tab-pane fade show active" id="today" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary mb-0">
                    <i class="fas fa-calendar-day me-2"></i>
                    جلسات اليوم
                </h5>
                <small class="text-muted">الجلسات المجدولة لتاريخ {{ date('d/m/Y') }}</small>
            </div>
            
            @if(count($classifiedSessions['today']) > 0)
                <div class="row">
                    @foreach($classifiedSessions['today'] as $session)
                        <div class="col-lg-4 col-md-6 mb-3">
                            @include('sessions.partials.session_card_compact', ['session' => $session])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times text-muted fs-1 mb-3"></i>
                    <h5 class="text-muted">لا توجد جلسات مجدولة لليوم</h5>
                    <p class="text-muted">يمكنك إضافة جلسة جديدة من الزر أعلاه</p>
                </div>
            @endif
        </div>

        <!-- Upcoming Sessions -->
        <div class="tab-pane fade" id="upcoming" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-info mb-0">
                    <i class="fas fa-clock me-2"></i>
                    الجلسات القادمة
                </h5>
                <small class="text-muted">الجلسات المجدولة للمستقبل</small>
            </div>
            
            @if(count($classifiedSessions['upcoming']) > 0)
                <div class="row">
                    @foreach($classifiedSessions['upcoming'] as $session)
                        <div class="col-lg-4 col-md-6 mb-3">
                            @include('sessions.partials.session_card_compact', ['session' => $session])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clock text-muted fs-1 mb-3"></i>
                    <h5 class="text-muted">لا توجد جلسات قادمة</h5>
                    <p class="text-muted">جميع الجلسات المجدولة قد انتهت أو تم إلغاؤها</p>
                </div>
            @endif
        </div>

        <!-- Missed Sessions -->
        <div class="tab-pane fade" id="missed" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    الجلسات الفائتة
                </h5>
                <small class="text-muted">الجلسات التي انتهى وقتها ولم يُسجل الحضور</small>
            </div>
            
            @if(count($classifiedSessions['missed']) > 0)
                <div class="row">
                    @foreach($classifiedSessions['missed'] as $session)
                        <div class="col-lg-4 col-md-6 mb-3">
                            @include('sessions.partials.session_card_compact', ['session' => $session])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle text-success fs-1 mb-3"></i>
                    <h5 class="text-success">ممتاز! لا توجد جلسات فائتة</h5>
                    <p class="text-muted">جميع الجلسات تم تسجيل الحضور فيها في الوقت المحدد</p>
                </div>
            @endif
        </div>

        <!-- Completed Sessions -->
        <div class="tab-pane fade" id="completed" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    الجلسات المكتملة
                </h5>
                <small class="text-muted">الجلسات التي تم تسجيل الحضور فيها</small>
            </div>
            
            @if(count($classifiedSessions['completed']) > 0)
                <div class="row">
                    @foreach($classifiedSessions['completed'] as $session)
                        <div class="col-lg-4 col-md-6 mb-3">
                            @include('sessions.partials.session_card_compact', ['session' => $session])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-hourglass-start text-muted fs-1 mb-3"></i>
                    <h5 class="text-muted">لا توجد جلسات مكتملة بعد</h5>
                    <p class="text-muted">ابدأ بتسجيل الحضور للجلسات المجدولة</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.nav-pills .nav-link {
    border-radius: 0;
    border: none;
    padding: 1rem 1.5rem;
    color: #6c757d;
    background: transparent;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    color: white;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.badge {
    font-size: 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
    
    // Tab switching with smooth transitions
    const tabs = document.querySelectorAll('[data-bs-toggle="pill"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const target = document.querySelector(e.target.getAttribute('href'));
            target.style.opacity = '0';
            setTimeout(() => {
                target.style.opacity = '1';
            }, 100);
        });
    });
});
</script>
@endsection

