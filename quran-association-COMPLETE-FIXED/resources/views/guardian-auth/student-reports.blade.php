@extends('layouts.guardian')

@section('title', 'تقارير الطالب - ' . $student->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Reports Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">
                                <i class="fas fa-chart-line me-2"></i>
                                تقارير الطالب: {{ $student->name }}
                            </h2>
                            <p class="mb-0 opacity-75">
                                تقارير شاملة للحضور والغياب والنقاط والأداء العام
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-chart-bar fa-4x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['total_sessions'] ?? 0 }}</h4>
                    <small>إجمالي الجلسات</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['present_count'] ?? 0 }}</h4>
                    <small>أيام الحضور</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['absent_count'] ?? 0 }}</h4>
                    <small>أيام الغياب</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['late_count'] ?? 0 }}</h4>
                    <small>مرات التأخير</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-star fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['total_points'] ?? 0 }}</h4>
                    <small>إجمالي النقاط</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $stats['attendance_rate'] ?? 0 }}%</h4>
                    <small>نسبة الحضور</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analysis -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        توزيع الحضور والغياب
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="p-3">
                                <div class="h2 text-success">{{ $stats['present_count'] ?? 0 }}</div>
                                <div class="text-muted">حاضر</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $stats['present_percentage'] ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3">
                                <div class="h2 text-danger">{{ $stats['absent_count'] ?? 0 }}</div>
                                <div class="text-muted">غائب</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $stats['absent_percentage'] ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3">
                                <div class="h2 text-warning">{{ $stats['late_count'] ?? 0 }}</div>
                                <div class="text-muted">متأخر</div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $stats['late_percentage'] ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        تحليل النقاط
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-3">
                                <div class="h3 text-primary">{{ $stats['total_points'] ?? 0 }}</div>
                                <div class="text-muted">إجمالي النقاط</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3">
                                <div class="h3 text-info">{{ $stats['average_points'] ?? 0 }}</div>
                                <div class="text-muted">متوسط النقاط</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($stats['total_points'] > 0)
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>التقدم نحو الهدف</span>
                            <span>{{ min(100, ($stats['total_points'] / 100) * 100) }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-gradient" style="width: {{ min(100, ($stats['total_points'] / 100) * 100) }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Absences Alert -->
    @if($recentAbsences && $recentAbsences->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">تنبيه: حالات غياب حديثة</h5>
                        <p class="mb-0">
                            تم تسجيل {{ $recentAbsences->count() }} حالة غياب في الأسبوعين الماضيين.
                            يرجى متابعة حضور الطالب بانتظام.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Detailed Attendance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        سجل الحضور التفصيلي
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary active" data-filter="all">
                            الكل
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" data-filter="present">
                            حاضر
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-filter="absent">
                            غائب
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" data-filter="late">
                            متأخر
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($attendanceRecords && $attendanceRecords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover" id="attendanceTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>اليوم</th>
                                        <th>الحالة</th>
                                        <th>النقاط</th>
                                        <th>وقت الوصول</th>
                                        <th>الملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceRecords as $record)
                                    <tr data-status="{{ $record->status }}">
                                        <td>{{ $record->date->format('Y-m-d') }}</td>
                                        <td>{{ $record->date->format('l') }}</td>
                                        <td>
                                            @if($record->status == 'present')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>حاضر
                                                </span>
                                            @elseif($record->status == 'absent')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>غائب
                                                </span>
                                            @elseif($record->status == 'late')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>متأخر
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">غير محدد</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $record->points ?? 0 }}</span>
                                        </td>
                                        <td>{{ $record->arrival_time ?? '-' }}</td>
                                        <td>{{ $record->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if(method_exists($attendanceRecords, 'links'))
                            <div class="d-flex justify-content-center mt-3">
                                {{ $attendanceRecords->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">لا توجد سجلات حضور</h4>
                            <p class="text-muted">لم يتم تسجيل أي حضور للطالب حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ route('guardian.student', $student->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-user me-1"></i>
                    تفاصيل الطالب
                </a>
                <a href="{{ route('guardian.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-1"></i>
                    العودة للوحة التحكم
                </a>
                <button class="btn btn-outline-success" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>
                    طباعة التقرير
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('[data-filter]');
    const tableRows = document.querySelectorAll('#attendanceTable tbody tr');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter rows
            tableRows.forEach(row => {
                if (filter === 'all' || row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>

<style>
@media print {
    .btn, .card-header .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
@endsection

