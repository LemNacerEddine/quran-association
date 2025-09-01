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
                    <h4 class="text-warning mb-0">{{ $stats['average_attendance'] }}%</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pb-0">
            <ul class="nav nav-pills nav-fill" id="sessionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeSection === 'today' ? 'active' : '' }}" 
                       href="{{ route('sessions.index', ['section' => 'today']) }}">
                        <i class="fas fa-calendar-day me-2"></i>
                        جلسات اليوم
                        <span class="badge bg-info ms-2">{{ $stats['today_sessions'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeSection === 'upcoming' ? 'active' : '' }}" 
                       href="{{ route('sessions.index', ['section' => 'upcoming']) }}">
                        <i class="fas fa-clock me-2"></i>
                        الجلسات القادمة
                        <span class="badge bg-primary ms-2">{{ $stats['upcoming_sessions'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeSection === 'missed' ? 'active' : '' }}" 
                       href="{{ route('sessions.index', ['section' => 'missed']) }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        الجلسات الفائتة
                        <span class="badge bg-danger ms-2">{{ $stats['missed_sessions'] }}</span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeSection === 'completed' ? 'active' : '' }}" 
                       href="{{ route('sessions.index', ['section' => 'completed']) }}">
                        <i class="fas fa-check-circle me-2"></i>
                        الجلسات المكتملة
                        <span class="badge bg-success ms-2">{{ $stats['completed_sessions'] }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <!-- Section Title -->
            <div class="d-flex align-items-center mb-4">
                @if($activeSection === 'today')
                    <i class="fas fa-calendar-day text-info fs-4 me-3"></i>
                    <div>
                        <h5 class="mb-1">جلسات اليوم</h5>
                        <p class="text-muted mb-0">الجلسات المجدولة لتاريخ {{ now()->format('d/m/Y') }}</p>
                    </div>
                @elseif($activeSection === 'upcoming')
                    <i class="fas fa-clock text-primary fs-4 me-3"></i>
                    <div>
                        <h5 class="mb-1">الجلسات القادمة</h5>
                        <p class="text-muted mb-0">الجلسات المجدولة للأيام القادمة</p>
                    </div>
                @elseif($activeSection === 'missed')
                    <i class="fas fa-exclamation-triangle text-danger fs-4 me-3"></i>
                    <div>
                        <h5 class="mb-1">الجلسات الفائتة</h5>
                        <p class="text-muted mb-0">الجلسات التي انتهى وقتها ولم يتم تسجيل الحضور</p>
                    </div>
                @elseif($activeSection === 'completed')
                    <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                    <div>
                        <h5 class="mb-1">الجلسات المكتملة</h5>
                        <p class="text-muted mb-0">الجلسات التي تم تسجيل الحضور فيها</p>
                    </div>
                @endif
            </div>

            <!-- Sessions Grid -->
            @if(count($sectionSessions) > 0)
                <div class="row">
                    @foreach($sectionSessions as $session)
                        <div class="col-lg-6 col-xl-4 mb-4">
                            @include('sessions.partials.session_card', ['session' => $session])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        @if($activeSection === 'today')
                            <i class="fas fa-calendar-day text-muted" style="font-size: 4rem;"></i>
                        @elseif($activeSection === 'upcoming')
                            <i class="fas fa-clock text-muted" style="font-size: 4rem;"></i>
                        @elseif($activeSection === 'missed')
                            <i class="fas fa-exclamation-triangle text-muted" style="font-size: 4rem;"></i>
                        @elseif($activeSection === 'completed')
                            <i class="fas fa-check-circle text-muted" style="font-size: 4rem;"></i>
                        @endif
                    </div>
                    <h5 class="text-muted mb-2">لا توجد جلسات</h5>
                    <p class="text-muted">
                        @if($activeSection === 'today')
                            لا توجد جلسات مجدولة لليوم
                        @elseif($activeSection === 'upcoming')
                            لا توجد جلسات قادمة
                        @elseif($activeSection === 'missed')
                            لا توجد جلسات فائتة
                        @elseif($activeSection === 'completed')
                            لا توجد جلسات مكتملة
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.nav-pills .nav-link {
    border-radius: 0.5rem;
    margin: 0 0.25rem;
    transition: all 0.3s ease;
    color: #6c757d;
    background: transparent;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.nav-pills .nav-link.active .badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: white;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
}
</style>
@endsection

