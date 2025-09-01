@extends('layouts.dashboard')

@section('title', 'لوحة التحكم الرئيسية')

@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة التحكم</li>
@endsection

@section('content')
<div class="fade-in">
    <!-- Page Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">لوحة التحكم الرئيسية</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-2"></i>تحديث البيانات
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickActionModal">
                <i class="fas fa-plus me-2"></i>إجراء سريع
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stats-number">{{ $totalStudents }}</div>
                <div class="stats-label">إجمالي الطلاب</div>
                <small class="mt-2 d-block">
                    <i class="fas fa-arrow-up text-success"></i>
                    {{ $newStudentsThisMonth }} طالب جديد هذا الشهر
                </small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                <div class="stats-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stats-number">{{ $totalTeachers }}</div>
                <div class="stats-label">المعلمين</div>
                <small class="mt-2 d-block">
                    <i class="fas fa-check-circle text-success"></i>
                    جميعهم نشطون
                </small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ $totalCircles }}</div>
                <div class="stats-label">الحلقات</div>
                <small class="mt-2 d-block">
                    <i class="fas fa-calendar-check text-success"></i>
                    {{ $todayAttendance }} حضور اليوم
                </small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                <div class="stats-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stats-number">{{ number_format($totalPoints) }}</div>
                <div class="stats-label">إجمالي النقاط</div>
                <small class="mt-2 d-block">
                    <i class="fas fa-trophy text-warning"></i>
                    {{ $averagePointsPerStudent }} متوسط لكل طالب
                </small>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Attendance Chart -->
        <div class="col-xl-8 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        إحصائيات الحضور الأسبوعية
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            آخر 7 أيام
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="updateChart('attendance', 'week')">آخر أسبوع</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateChart('attendance', 'month')">آخر شهر</a></li>
                        </ul>
                    </div>
                </div>
                <canvas id="attendanceChart" height="100"></canvas>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-xl-4 mb-4">
            <div class="dashboard-card h-100">
                <h5 class="card-title mb-3">
                    <i class="fas fa-tachometer-alt text-success me-2"></i>
                    إحصائيات سريعة
                </h5>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>معدل الحضور اليومي</span>
                        <strong class="text-success">{{ $averageAttendanceRate }}%</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $averageAttendanceRate }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>الطلاب النشطون</span>
                        <strong class="text-primary">{{ $activeStudents }}/{{ $totalStudents }}</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $totalStudents > 0 ? ($activeStudents / $totalStudents) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>نقاط هذا الشهر</span>
                        <strong class="text-warning">{{ number_format($monthlyPoints) }}</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ $totalPoints > 0 ? ($monthlyPoints / $totalPoints) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-outline-primary btn-sm" onclick="showDetailedStats()">
                        <i class="fas fa-chart-pie me-1"></i>
                        عرض التفاصيل
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Top Students -->
        <div class="col-xl-6 mb-4">
            <div class="dashboard-card">
                <h5 class="card-title mb-3">
                    <i class="fas fa-trophy text-warning me-2"></i>
                    أفضل الطلاب (حسب النقاط)
                </h5>
                
                @if($topStudents->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($topStudents as $index => $student)
                            <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <div class="rank-badge me-3">
                                        @if($index == 0)
                                            <i class="fas fa-crown text-warning"></i>
                                        @elseif($index == 1)
                                            <i class="fas fa-medal text-secondary"></i>
                                        @elseif($index == 2)
                                            <i class="fas fa-award text-warning"></i>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $student->name }}</h6>
                                        <small class="text-muted">{{ $student->circle->name ?? 'غير محدد' }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong class="text-primary">{{ number_format($student->total_points ?? 0) }}</strong>
                                    <small class="text-muted d-block">نقطة</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد نقاط حفظ مسجلة بعد</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-xl-6 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock text-info me-2"></i>
                        آخر الأنشطة
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                
                @if($recentActivities->count() > 0)
                    <div class="activity-timeline">
                        @foreach($recentActivities->take(6) as $activity)
                            <div class="activity-item">
                                <div class="activity-icon bg-{{ $activity['color'] }}">
                                    <i class="{{ $activity['icon'] }}"></i>
                                </div>
                                <div class="activity-content">
                                    <h6 class="activity-title">{{ $activity['title'] }}</h6>
                                    <p class="activity-description">{{ $activity['description'] }}</p>
                                    <small class="activity-time text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $activity['time']->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">لا توجد أنشطة حديثة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Circle Distribution Chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-success me-2"></i>
                        توزيع الطلاب حسب الحلقات
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleChartType()">
                        <i class="fas fa-exchange-alt me-1"></i>
                        تغيير النوع
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <canvas id="circleChart" height="80"></canvas>
                    </div>
                    <div class="col-md-4">
                        <div class="circle-legend">
                            @foreach($circleDistribution as $index => $circle)
                                <div class="legend-item d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="legend-color" style="background-color: {{ ['#2E7D32', '#4CAF50', '#81C784', '#A5D6A7', '#C8E6C9'][$index % 5] }}"></div>
                                        <span>{{ $circle->name }}</span>
                                    </div>
                                    <strong>{{ $circle->student_count }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Modal -->
<div class="modal fade" id="quickActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">الإجراءات السريعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('students.create') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <span>إضافة طالب</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('teachers.create') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                            <span>إضافة معلم</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('circles.create') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <span>إضافة حلقة</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('attendance.create') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <span>تسجيل حضور</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .activity-timeline {
        position: relative;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
        position: relative;
    }

    .activity-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 20px;
        top: 40px;
        width: 2px;
        height: calc(100% + 10px);
        background: #e9ecef;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-left: 15px;
        flex-shrink: 0;
        position: relative;
        z-index: 2;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-size: 0.9rem;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .activity-description {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .activity-time {
        font-size: 0.75rem;
    }

    .rank-badge {
        width: 30px;
        text-align: center;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-left: 8px;
    }

    .legend-item {
        padding: 5px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .legend-item:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('scripts')
<script>
    // Chart configurations
    const chartColors = ['#2E7D32', '#4CAF50', '#81C784', '#A5D6A7', '#C8E6C9'];
    
    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($weeklyLabels) !!},
            datasets: [{
                label: 'عدد الحاضرين',
                data: {!! json_encode($weeklyAttendanceData) !!},
                borderColor: '#2E7D32',
                backgroundColor: 'rgba(46, 125, 50, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2E7D32',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
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
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Circle Distribution Chart
    const circleCtx = document.getElementById('circleChart').getContext('2d');
    let circleChartType = 'doughnut';
    
    const circleChart = new Chart(circleCtx, {
        type: circleChartType,
        data: {
            labels: {!! json_encode($circleDistribution->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($circleDistribution->pluck('student_count')) !!},
                backgroundColor: chartColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Functions
    function refreshDashboard() {
        location.reload();
    }

    function updateChart(type, period) {
        // Implementation for updating charts based on period
        console.log('Updating chart:', type, period);
    }

    function showDetailedStats() {
        // Implementation for showing detailed statistics
        console.log('Showing detailed stats');
    }

    function toggleChartType() {
        circleChartType = circleChartType === 'doughnut' ? 'bar' : 'doughnut';
        circleChart.destroy();
        
        const newChart = new Chart(circleCtx, {
            type: circleChartType,
            data: {
                labels: {!! json_encode($circleDistribution->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($circleDistribution->pluck('student_count')) !!},
                    backgroundColor: chartColors,
                    borderWidth: 0
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
                scales: circleChartType === 'bar' ? {
                    y: {
                        beginAtZero: true
                    }
                } : {}
            }
        });
    }

    // Auto refresh every 5 minutes
    setInterval(function() {
        // You can implement auto-refresh logic here
    }, 300000);
</script>
@endsection

