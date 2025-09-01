@extends('layouts.guardian')

@section('title', 'لوحة التحكم - ' . $guardian->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">
                                <i class="fas fa-home me-2"></i>
                                مرحباً بك، {{ $guardian->name }}
                            </h2>
                            <p class="mb-0 opacity-75">
                                آخر دخول: {{ $guardian->last_login_at ? $guardian->last_login_at->format('Y-m-d H:i') : 'أول مرة' }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-user-circle fa-4x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_students'] }}</h3>
                            <small>إجمالي الأولاد</small>
                        </div>
                        <i class="fas fa-graduation-cap fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_points'] }}</h3>
                            <small>إجمالي النقاط</small>
                        </div>
                        <i class="fas fa-star fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_attendance'] }}</h3>
                            <small>إجمالي الحضور</small>
                        </div>
                        <i class="fas fa-check fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_absences'] }}</h3>
                            <small>إجمالي الغيابات</small>
                        </div>
                        <i class="fas fa-times fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics Row -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_sessions'] }}</h3>
                            <small>إجمالي الجلسات</small>
                        </div>
                        <i class="fas fa-calendar fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card bg-gradient-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['avg_attendance_rate'] }}%</h3>
                            <small>معدل الحضور</small>
                        </div>
                        <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card bg-gradient-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['active_circles'] }}</h3>
                            <small>حلقات نشطة</small>
                        </div>
                        <i class="fas fa-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance Alert - Real Data Based on Guardian's Students -->
    @php
        // الحصول على حالات الغياب الحديثة لأطفال ولي الأمر فقط
        $recentAbsences = collect();
        foreach($guardian->students as $student) {
            $studentAbsences = \DB::table('attendance')
                ->join('class_sessions', 'attendance.session_id', '=', 'class_sessions.id')
                ->where('attendance.student_id', $student->id)
                ->where('attendance.status', 'absent')
                ->where('class_sessions.session_date', '>=', now()->subWeek())
                ->select('attendance.*', 'class_sessions.session_date')
                ->get();
            
            foreach($studentAbsences as $absence) {
                $recentAbsences->push((object)[
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'date' => \Carbon\Carbon::parse($absence->session_date),
                    'reason' => $absence->notes ?? 'غياب'
                ]);
            }
        }
    @endphp

    @if($recentAbsences->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <!-- Using custom div instead of Bootstrap alert to prevent any dismissible behavior -->
            <div class="custom-alert-warning" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left: 4px solid #f39c12; border-radius: 0.375rem; padding: 1rem;">
                <h6 class="alert-heading text-dark mb-2">
                    <i class="fas fa-exclamation-triangle me-2 text-warning" style="animation: gentle-pulse 3s infinite;"></i>
                    تنبيه: حالات غياب حديثة
                </h6>
                <p class="text-dark mb-2">
                    تم تسجيل <strong>{{ $recentAbsences->count() }} حالة غياب</strong> في الأسبوع الماضي:
                </p>
                
                @foreach($recentAbsences as $absence)
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-dark">
                        <strong>{{ $absence->student_name }}</strong> - {{ $absence->date->format('Y-m-d') }} ({{ $absence->reason }})
                    </span>
                    <div class="d-flex gap-1">
                        <a href="{{ route('guardian.student.reports', $absence->student_id) }}" class="btn btn-warning btn-sm">
                            تقارير {{ explode(' ', $absence->student_name)[0] }}
                        </a>
                        <a href="{{ route('guardian.student', $absence->student_id) }}" class="btn btn-outline-warning btn-sm">
                            تفاصيل {{ explode(' ', $absence->student_name)[0] }}
                        </a>
                    </div>
                </div>
                @endforeach
                
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>
                    هذا التنبيه سيبقى ظاهراً حتى تتابع حالات الغياب مع الطلاب
                </small>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes gentle-pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .custom-alert-warning {
            /* Custom alert that cannot be dismissed */
            position: relative;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .custom-alert-warning * {
            /* Prevent any child elements from being hidden */
            display: inherit !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Override any Bootstrap alert dismissible behavior */
        .custom-alert-warning .btn-close,
        .custom-alert-warning [data-bs-dismiss],
        .custom-alert-warning .alert-dismissible {
            display: none !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }
        
        /* Prevent JavaScript from hiding this element */
        .custom-alert-warning.fade,
        .custom-alert-warning.show,
        .custom-alert-warning.hide,
        .custom-alert-warning.d-none {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        @media (max-width: 768px) {
            .custom-alert-warning .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .custom-alert-warning .d-flex .d-flex {
                width: 100%;
                justify-content: stretch;
                margin-top: 0.5rem;
            }
            
            .custom-alert-warning .btn {
                flex: 1;
            }
        }
    </style>
    
    <script>
        // Prevent any JavaScript from hiding the alert
        document.addEventListener('DOMContentLoaded', function() {
            const alertElement = document.querySelector('.custom-alert-warning');
            if (alertElement) {
                // Create a MutationObserver to watch for changes
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                            // Restore visibility if someone tries to hide it
                            if (alertElement.style.display === 'none' || 
                                alertElement.style.visibility === 'hidden' || 
                                alertElement.style.opacity === '0') {
                                alertElement.style.display = 'block';
                                alertElement.style.visibility = 'visible';
                                alertElement.style.opacity = '1';
                            }
                        }
                        if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                            // Remove any classes that might hide the element
                            alertElement.classList.remove('d-none', 'hide', 'hidden');
                            alertElement.classList.add('d-block');
                        }
                    });
                });
                
                // Start observing
                observer.observe(alertElement, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });
                
                // Override common hiding methods
                alertElement.hide = function() { return false; };
                alertElement.remove = function() { return false; };
                alertElement.parentNode.removeChild = function(child) {
                    if (child === alertElement) return false;
                    return Node.prototype.removeChild.call(this, child);
                };
            }
        });
    </script>
    @endif

    <!-- Students List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        أولادي المسجلين
                    </h5>
                </div>
                <div class="card-body">
                    @if($studentsWithStats->count() > 0)
                        <div class="row">
                            @foreach($studentsWithStats as $student)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-3">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $student->name }}</h6>
                                                    @if($student->pivot->is_primary)
                                                        <span class="badge bg-warning text-dark">أساسي</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted d-block">نوع العلاقة</small>
                                            <span class="badge bg-info">
                                                {{ $student->pivot->relationship_type ?? 'ولي أمر' }}
                                            </span>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">الحلقة</small>
                                            <strong>{{ $student->circle->name ?? 'غير محدد' }}</strong>
                                            @if($student->circle && $student->circle->teacher)
                                                <br><small class="text-muted">المعلم: {{ $student->circle->teacher->name }}</small>
                                            @endif
                                        </div>

                                        <!-- إحصائيات الطالب الصحيحة -->
                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <div class="text-primary">
                                                    <strong>{{ $student->total_points }}</strong>
                                                    <br><small>نقاط</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-success">
                                                    <strong>{{ $student->present_count }}</strong>
                                                    <br><small>حضور</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-danger">
                                                    <strong>{{ $student->absent_count }}</strong>
                                                    <br><small>غياب</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- معدل الحضور -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">معدل الحضور</small>
                                                <small class="fw-bold">{{ $student->attendance_percentage }}%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar 
                                                    @if($student->attendance_percentage >= 80) bg-success
                                                    @elseif($student->attendance_percentage >= 60) bg-warning
                                                    @else bg-danger
                                                    @endif" 
                                                    style="width: {{ $student->attendance_percentage }}%">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- إجمالي الجلسات -->
                                        <div class="mb-3">
                                            <small class="text-muted">إجمالي الجلسات: <strong>{{ $student->total_sessions }}</strong></small>
                                        </div>
                                                @switch($student->pivot->relationship)
                                                    @case('father') الأب @break
                                                    @case('mother') الأم @break
                                                    @case('guardian') ولي الأمر @break
                                                    @default أخرى
                                                @endswitch
                                            </span>
                                        </div>
                                        
                                        @if($student->circle)
                                        <div class="mb-3">
                                            <small class="text-muted d-block">الحلقة</small>
                                            <div class="fw-bold text-success">{{ $student->circle->name }}</div>
                                            @if($student->circle->teacher)
                                                <small class="text-muted">
                                                    المعلم: {{ $student->circle->teacher->name }}
                                                </small>
                                            @endif
                                        </div>
                                        @else
                                        <div class="mb-3">
                                            <small class="text-muted d-block">الحلقة</small>
                                            <div class="text-muted">غير مسجل في حلقة</div>
                                        </div>
                                        @endif

                                        <!-- Quick Stats -->
                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <div class="small text-success fw-bold">15</div>
                                                <div class="small text-muted">حضور</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="small text-danger fw-bold">3</div>
                                                <div class="small text-muted">غياب</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="small text-warning fw-bold">85</div>
                                                <div class="small text-muted">نقاط</div>
                                            </div>
                                        </div>

                                        <!-- Recent Attendance Status -->
                                        <div class="mb-3">
                                            <div class="small text-muted mb-1">آخر 5 أيام</div>
                                            <div class="d-flex gap-1">
                                                <span class="badge bg-success" title="حاضر">✓</span>
                                                <span class="badge bg-success" title="حاضر">✓</span>
                                                <span class="badge bg-danger" title="غائب">✗</span>
                                                <span class="badge bg-success" title="حاضر">✓</span>
                                                <span class="badge bg-warning" title="متأخر">⏰</span>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('guardian.student', $student->id) }}" 
                                               class="btn btn-sm btn-outline-primary flex-fill">
                                                <i class="fas fa-eye me-1"></i> عرض
                                            </a>
                                            <a href="{{ route('guardian.student.reports', $student->id) }}" 
                                               class="btn btn-sm btn-outline-success flex-fill">
                                                <i class="fas fa-chart-line me-1"></i> التقارير
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد أولاد مسجلين</h5>
                            <p class="text-muted">لم يتم ربط أي طلاب بحسابك حتى الآن</p>
                            <p class="text-muted">
                                <small>يرجى التواصل مع إدارة الجمعية لربط أولادك بحسابك</small>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 > * {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection



    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('guardian.profile') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-edit me-2"></i>
                                تحديث الملف الشخصي
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button class="btn btn-outline-info w-100" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>
                                طباعة التقارير
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="tel:{{ $guardian->phone }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-phone me-2"></i>
                                الاتصال بالإدارة
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <form method="POST" action="{{ route('guardian.logout') }}" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

