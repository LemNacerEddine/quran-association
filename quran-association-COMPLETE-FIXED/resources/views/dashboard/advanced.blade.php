@extends('layouts.dashboard')

@section('title', 'لوحة التحكم الشاملة')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
<style>
.dashboard-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25);
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.stat-card.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card.info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.chart-container {
    position: relative;
    height: 300px;
    margin: 1rem 0;
}

.activity-item {
    padding: 0.75rem;
    border-left: 3px solid #e3e6f0;
    margin-bottom: 0.5rem;
    background: #f8f9fc;
    border-radius: 0 5px 5px 0;
    transition: all 0.2s ease;
}

.activity-item:hover {
    border-left-color: #5a5c69;
    background: #eaecf4;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring-circle {
    transition: stroke-dasharray 0.35s;
    transform-origin: 50% 50%;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #e74a3b;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quick-action-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    margin: 0.25rem;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.weather-widget {
    background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
    color: white;
    border-radius: 15px;
    padding: 1rem;
}

.calendar-widget {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header with Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt text-primary"></i>
                لوحة التحكم الشاملة
            </h1>
            <p class="mb-0 text-muted">مرحباً بك، {{ auth()->user()->name ?? 'المدير' }} - {{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="quick-actions">
            <a href="{{ route('students.create') }}" class="quick-action-btn">
                <i class="fas fa-user-plus"></i> إضافة طالب
            </a>
            <a href="{{ route('circles.create') }}" class="quick-action-btn">
                <i class="fas fa-plus-circle"></i> إنشاء حلقة
            </a>
            <a href="{{ route('sessions.create') }}" class="quick-action-btn">
                <i class="fas fa-calendar-plus"></i> جلسة جديدة
            </a>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">إجمالي الطلاب</div>
                        <div class="h4 mb-0 font-weight-bold">{{ $statistics['total_students'] ?? 0 }}</div>
                        <small class="text-white-50">
                            <i class="fas fa-arrow-up"></i> +{{ $statistics['new_students_this_month'] ?? 0 }} هذا الشهر
                        </small>
                    </div>
                    <div class="text-white-50">
                        <i class="fas fa-graduation-cap fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">معدل الحضور</div>
                        <div class="h4 mb-0 font-weight-bold">{{ $statistics['attendance_rate'] ?? 0 }}%</div>
                        <small class="text-white-50">
                            <i class="fas fa-chart-line"></i> الأسبوع الماضي
                        </small>
                    </div>
                    <div class="text-white-50">
                        <i class="fas fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">نقاط الحفظ</div>
                        <div class="h4 mb-0 font-weight-bold">{{ $statistics['total_memorization_points'] ?? 0 }}</div>
                        <small class="text-white-50">
                            <i class="fas fa-star"></i> هذا الشهر
                        </small>
                    </div>
                    <div class="text-white-50">
                        <i class="fas fa-book-open fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-uppercase mb-1">الحلقات النشطة</div>
                        <div class="h4 mb-0 font-weight-bold">{{ $statistics['active_circles'] ?? 0 }}</div>
                        <small class="text-white-50">
                            <i class="fas fa-users"></i> من {{ $statistics['total_circles'] ?? 0 }} حلقة
                        </small>
                    </div>
                    <div class="text-white-50">
                        <i class="fas fa-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="row mb-4">
        <!-- Attendance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card dashboard-card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area"></i> إحصائيات الحضور الشهرية
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fas fa-filter"></i> فلترة
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="updateChart('week')">الأسبوع الماضي</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('month')">الشهر الماضي</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('quarter')">الربع الماضي</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="col-xl-4 col-lg-5">
            <div class="card dashboard-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy"></i> أداء الحلقات
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($topCircles ?? [] as $circle)
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                            <div class="progress-ring" style="width: 50px; height: 50px;">
                                <svg width="50" height="50">
                                    <circle cx="25" cy="25" r="20" stroke="#e3e6f0" stroke-width="4" fill="transparent"/>
                                    <circle cx="25" cy="25" r="20" stroke="#1cc88a" stroke-width="4" fill="transparent"
                                            stroke-dasharray="{{ $circle['performance'] * 1.25 }} 125"
                                            class="progress-ring-circle"/>
                                </svg>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 0.7rem; font-weight: bold;">
                                    {{ $circle['performance'] }}%
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold">{{ $circle['name'] }}</div>
                            <small class="text-muted">{{ $circle['teacher'] }}</small>
                            <div class="text-xs text-success">{{ $circle['students_count'] }} طالب</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="row mb-4">
        <!-- Memorization Progress -->
        <div class="col-xl-6 col-lg-6">
            <div class="card dashboard-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-book"></i> تقدم الحفظ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="memorizationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Performance -->
        <div class="col-xl-6 col-lg-6">
            <div class="card dashboard-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chalkboard-teacher"></i> أداء المعلمين
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="teacherChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Feed and Notifications -->
    <div class="row mb-4">
        <!-- Recent Activities -->
        <div class="col-xl-8 col-lg-7">
            <div class="card dashboard-card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> الأنشطة الأخيرة
                    </h6>
                    <a href="{{ route('activities.all') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @foreach($recentActivities ?? [] as $activity)
                    <div class="activity-item">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                @switch($activity['type'])
                                    @case('attendance')
                                        <i class="fas fa-user-check text-success"></i>
                                        @break
                                    @case('memorization')
                                        <i class="fas fa-star text-warning"></i>
                                        @break
                                    @case('new_student')
                                        <i class="fas fa-user-plus text-info"></i>
                                        @break
                                    @default
                                        <i class="fas fa-info-circle text-muted"></i>
                                @endswitch
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $activity['title'] }}</div>
                                <small class="text-muted">{{ $activity['description'] }}</small>
                            </div>
                            <div class="text-xs text-muted">
                                {{ $activity['time'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Notifications and Quick Info -->
        <div class="col-xl-4 col-lg-5">
            <!-- Notifications -->
            <div class="card dashboard-card mb-3">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell"></i> الإشعارات
                        @if(($unreadNotifications ?? 0) > 0)
                            <span class="notification-badge">{{ $unreadNotifications }}</span>
                        @endif
                    </h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                        تحديد الكل كمقروء
                    </button>
                </div>
                <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                    @forelse($notifications ?? [] as $notification)
                    <div class="d-flex align-items-center mb-2 p-2 {{ $notification['is_read'] ? 'text-muted' : 'bg-light' }} rounded">
                        <div class="mr-2">
                            <i class="fas fa-circle text-primary" style="font-size: 0.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold text-xs">{{ $notification['title'] }}</div>
                            <div class="text-xs">{{ $notification['message'] }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>لا توجد إشعارات جديدة</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Weather Widget -->
            <div class="weather-widget mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h5 mb-0">الرياض</div>
                        <div class="text-white-50">{{ now()->format('l') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="h3 mb-0">28°C</div>
                        <div class="text-white-50">
                            <i class="fas fa-sun"></i> مشمس
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mini Calendar -->
            <div class="calendar-widget">
                <div class="text-center">
                    <div class="h6 font-weight-bold text-primary mb-2">
                        {{ now()->format('F Y') }}
                    </div>
                    <div class="h2 font-weight-bold text-dark">
                        {{ now()->format('d') }}
                    </div>
                    <div class="text-muted">
                        {{ now()->format('l') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="row">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-server"></i> حالة النظام
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="font-weight-bold">قاعدة البيانات</div>
                                <small class="text-success">متصلة</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="font-weight-bold">خدمة الإشعارات</div>
                                <small class="text-success">تعمل</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="font-weight-bold">النسخ الاحتياطي</div>
                                <small class="text-warning">آخر نسخة: أمس</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 text-info">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="font-weight-bold">استخدام التخزين</div>
                                <small class="text-info">75% مستخدم</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Attendance Chart
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(attendanceCtx, {
    type: 'line',
    data: {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'معدل الحضور',
            data: [85, 88, 92, 89, 94, 91],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// Memorization Chart
const memorizationCtx = document.getElementById('memorizationChart').getContext('2d');
const memorizationChart = new Chart(memorizationCtx, {
    type: 'doughnut',
    data: {
        labels: ['مكتمل', 'قيد التقدم', 'لم يبدأ'],
        datasets: [{
            data: [35, 45, 20],
            backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Teacher Performance Chart
const teacherCtx = document.getElementById('teacherChart').getContext('2d');
const teacherChart = new Chart(teacherCtx, {
    type: 'bar',
    data: {
        labels: ['أحمد محمد', 'فاطمة علي', 'محمد سالم', 'عائشة أحمد'],
        datasets: [{
            label: 'نقاط الأداء',
            data: [95, 88, 92, 85],
            backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// Functions
function updateChart(period) {
    // Update chart data based on selected period
    console.log('Updating chart for period:', period);
}

function markAllAsRead() {
    // Mark all notifications as read
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    });
}

// Auto-refresh data every 5 minutes
setInterval(function() {
    // Refresh dashboard data
    location.reload();
}, 300000);
</script>
@endsection

