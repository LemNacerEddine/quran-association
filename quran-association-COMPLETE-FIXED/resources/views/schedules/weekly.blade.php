@extends('layouts.dashboard')

@section('title', 'الجدول الأسبوعي')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">الجدول الأسبوعي</h1>
            <p class="text-muted">عرض جدولة الحصص الأسبوعية</p>
        </div>
        <div>
            <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-list"></i> العرض التفصيلي
            </a>
            <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة جدولة جديدة
            </a>
        </div>
    </div>

    <!-- Weekly Schedule -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-week"></i> الجدول الأسبوعي للحصص
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered weekly-schedule">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="100">الوقت</th>
                            @foreach($days as $dayKey => $dayName)
                                <th class="text-center">{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $timeSlots = [
                                '06:00' => '06:00 - 07:30',
                                '07:00' => '07:00 - 08:30',
                                '08:00' => '08:00 - 09:30',
                                '09:00' => '09:00 - 10:30',
                                '10:00' => '10:00 - 11:30',
                                '15:00' => '15:00 - 16:30',
                                '16:00' => '16:00 - 17:30',
                                '17:00' => '17:00 - 18:30',
                                '18:00' => '18:00 - 19:30',
                                '19:00' => '19:00 - 20:30'
                            ];
                        @endphp
                        
                        @foreach($timeSlots as $time => $timeRange)
                            <tr>
                                <td class="bg-light font-weight-bold text-center align-middle">
                                    {{ $timeRange }}
                                </td>
                                @foreach($days as $dayKey => $dayName)
                                    <td class="schedule-cell" data-day="{{ $dayKey }}" data-time="{{ $time }}">
                                        @if(isset($schedules[$dayKey]))
                                            @foreach($schedules[$dayKey] as $schedule)
                                                @php
                                                    $startTime = $schedule->start_time->format('H:i');
                                                    $endTime = $schedule->end_time->format('H:i');
                                                    $cellTime = $time . ':00';
                                                @endphp
                                                @if($startTime <= $cellTime && $endTime > $cellTime)
                                                    <div class="schedule-item {{ $schedule->session_type }}" 
                                                         data-bs-toggle="tooltip" 
                                                         title="الحلقة: {{ $schedule->circle->name }} | المعلم: {{ $schedule->circle->teacher->name }} | المكان: {{ $schedule->location }}">
                                                        <div class="schedule-title">{{ $schedule->schedule_name }}</div>
                                                        <div class="schedule-circle">{{ $schedule->circle->name }}</div>
                                                        <div class="schedule-time">{{ $schedule->formatted_time }}</div>
                                                        <div class="schedule-teacher">{{ $schedule->circle->teacher->name }}</div>
                                                        @if($schedule->location)
                                                            <div class="schedule-location">
                                                                <i class="fas fa-map-marker-alt"></i> {{ $schedule->location }}
                                                            </div>
                                                        @endif
                                                        <div class="schedule-actions">
                                                            <a href="{{ route('schedules.show', $schedule) }}" class="btn btn-sm btn-outline-light">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-light">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">مفتاح الألوان</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="legend-item">
                                <div class="legend-color morning"></div>
                                <span>حصص صباحية</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="legend-item">
                                <div class="legend-color afternoon"></div>
                                <span>حصص ظهيرة</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="legend-item">
                                <div class="legend-color evening"></div>
                                <span>حصص مسائية</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات سريعة</h6>
                </div>
                <div class="card-body">
                    @php
                        $allSchedules = collect($schedules)->flatten();
                        $totalSchedules = $allSchedules->count();
                        $morningSchedules = $allSchedules->where('session_type', 'morning')->count();
                        $afternoonSchedules = $allSchedules->where('session_type', 'afternoon')->count();
                        $eveningSchedules = $allSchedules->where('session_type', 'evening')->count();
                    @endphp
                    
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ $totalSchedules }}</div>
                                <div class="stat-label">إجمالي الحصص</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <div class="stat-number text-warning">{{ $morningSchedules }}</div>
                                <div class="stat-label">صباحية</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <div class="stat-number text-info">{{ $afternoonSchedules }}</div>
                                <div class="stat-label">ظهيرة</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <div class="stat-number text-dark">{{ $eveningSchedules }}</div>
                                <div class="stat-label">مسائية</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.weekly-schedule {
    font-size: 0.9rem;
}

.schedule-cell {
    height: 80px;
    vertical-align: top;
    position: relative;
    padding: 5px;
}

.schedule-item {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 8px;
    margin: 2px;
    position: relative;
    height: 100%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.schedule-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.schedule-item.morning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.schedule-item.afternoon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.schedule-item.evening {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.schedule-title {
    font-weight: bold;
    font-size: 0.8rem;
    margin-bottom: 2px;
}

.schedule-circle {
    font-size: 0.7rem;
    opacity: 0.9;
    margin-bottom: 1px;
}

.schedule-time {
    font-size: 0.7rem;
    opacity: 0.8;
    margin-bottom: 1px;
}

.schedule-teacher {
    font-size: 0.7rem;
    opacity: 0.8;
    margin-bottom: 1px;
}

.schedule-location {
    font-size: 0.6rem;
    opacity: 0.7;
    margin-bottom: 2px;
}

.schedule-actions {
    position: absolute;
    top: 2px;
    right: 2px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.schedule-item:hover .schedule-actions {
    opacity: 1;
}

.schedule-actions .btn {
    padding: 2px 4px;
    margin: 0 1px;
    font-size: 0.7rem;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    margin-left: 8px;
}

.legend-color.morning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.legend-color.afternoon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.legend-color.evening {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.stat-item {
    margin-bottom: 10px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
}

.stat-label {
    font-size: 0.8rem;
    color: #6c757d;
}

@media (max-width: 768px) {
    .weekly-schedule {
        font-size: 0.7rem;
    }
    
    .schedule-cell {
        height: 60px;
    }
    
    .schedule-item {
        padding: 4px;
    }
    
    .schedule-title {
        font-size: 0.7rem;
    }
    
    .schedule-circle,
    .schedule-time,
    .schedule-teacher {
        font-size: 0.6rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

