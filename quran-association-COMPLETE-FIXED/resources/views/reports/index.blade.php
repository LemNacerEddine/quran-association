@extends('layouts.dashboard')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">التقارير والإحصائيات</h1>
            <p class="mb-0 text-muted">نظام شامل لمتابعة الأداء والإحصائيات</p>
        </div>
        <div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-download"></i> تصدير التقارير
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('reports.export', ['type' => 'attendance', 'format' => 'pdf']) }}">
                        <i class="fas fa-file-pdf"></i> تقرير الحضور (PDF)
                    </a>
                    <a class="dropdown-item" href="{{ route('reports.export', ['type' => 'memorization', 'format' => 'pdf']) }}">
                        <i class="fas fa-file-pdf"></i> تقرير الحفظ (PDF)
                    </a>
                    <a class="dropdown-item" href="{{ route('reports.export', ['type' => 'teachers', 'format' => 'excel']) }}">
                        <i class="fas fa-file-excel"></i> تقرير المعلمين (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الطلاب</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_students'] }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up"></i> {{ $statistics['active_students'] }} نشط
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">معدل الحضور الشهري</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['this_month_attendance'] }}%</div>
                            <div class="text-xs {{ $statistics['this_month_attendance'] >= $statistics['last_month_attendance'] ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $statistics['this_month_attendance'] >= $statistics['last_month_attendance'] ? 'up' : 'down' }}"></i>
                                {{ abs($statistics['this_month_attendance'] - $statistics['last_month_attendance']) }}% من الشهر الماضي
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">نقاط الحفظ الشهرية</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['this_month_memorization'] }}</div>
                            <div class="text-xs {{ $statistics['this_month_memorization'] >= $statistics['last_month_memorization'] ? 'text-success' : 'text-danger' }}">
                                <i class="fas fa-arrow-{{ $statistics['this_month_memorization'] >= $statistics['last_month_memorization'] ? 'up' : 'down' }}"></i>
                                {{ abs($statistics['this_month_memorization'] - $statistics['last_month_memorization']) }} من الشهر الماضي
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">الحلقات النشطة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['active_circles'] }}</div>
                            <div class="text-xs text-muted">
                                من أصل {{ $statistics['total_circles'] }} حلقة
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Navigation -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">تقارير الحضور</h5>
                    <p class="card-text text-muted">تقارير مفصلة عن حضور الطلاب والإحصائيات اليومية والشهرية</p>
                    <a href="{{ route('reports.attendance') }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i> عرض التقرير
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-book-open fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">تقارير الحفظ</h5>
                    <p class="card-text text-muted">متابعة تقدم الطلاب في الحفظ والدرجات والإنجازات</p>
                    <a href="{{ route('reports.memorization') }}" class="btn btn-success">
                        <i class="fas fa-eye"></i> عرض التقرير
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-chalkboard-teacher fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">تقارير المعلمين</h5>
                    <p class="card-text text-muted">تقييم أداء المعلمين وإحصائيات الحلقات المدارة</p>
                    <a href="{{ route('reports.teachers') }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> عرض التقرير
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title">تقارير الحلقات</h5>
                    <p class="card-text text-muted">إحصائيات شاملة عن أداء الحلقات وكفاءتها</p>
                    <a href="{{ route('reports.circles') }}" class="btn btn-warning">
                        <i class="fas fa-eye"></i> عرض التقرير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Performing Circles -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أفضل الحلقات أداءً</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>الحلقة</th>
                                    <th>المعلم</th>
                                    <th>عدد الطلاب</th>
                                    <th>نقاط الأداء</th>
                                    <th>التقييم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['top_performing_circles'] as $item)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $item['circle']->name }}</div>
                                        <small class="text-muted">{{ $item['circle']->level_name }}</small>
                                    </td>
                                    <td>{{ $item['circle']->teacher->name ?? 'غير محدد' }}</td>
                                    <td class="text-center">{{ $item['circle']->students->count() }}</td>
                                    <td class="text-center">
                                        <span class="font-weight-bold text-success">{{ number_format($item['performance_score'], 1) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $score = $item['performance_score'];
                                            $stars = round($score / 20); // Convert to 5-star rating
                                        @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $stars ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">النشاطات الأخيرة</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($statistics['recent_activities'] as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $activity['type'] == 'memorization' ? 'success' : 'info' }}"></div>
                            <div class="timeline-content">
                                <p class="timeline-text">{{ $activity['message'] }}</p>
                                <small class="text-muted">{{ $activity['created_at']->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Attendance Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معدل الحضور الأسبوعي</h6>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Memorization Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">نقاط الحفظ الشهرية</h6>
                </div>
                <div class="card-body">
                    <canvas id="memorizationChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
    max-height: 400px;
    overflow-y: auto;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline-content {
    background: #f8f9fc;
    padding: 10px 15px;
    border-radius: 5px;
    border-left: 3px solid #5a5c69;
}

.timeline-text {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #5a5c69;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Attendance Chart
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(attendanceCtx, {
    type: 'line',
    data: {
        labels: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
        datasets: [{
            label: 'معدل الحضور %',
            data: [85, 92, 78, 88, 95, 82, 90],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
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
    type: 'bar',
    data: {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'نقاط الحفظ',
            data: [450, 520, 380, 610, 580, 720],
            backgroundColor: '#1cc88a',
            borderColor: '#1cc88a',
            borderWidth: 1
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
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection

