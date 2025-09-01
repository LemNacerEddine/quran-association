@extends('layouts.dashboard')

@section('title', 'إدارة الجدولة')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">
                <i class="fas fa-calendar-alt me-2"></i>
                إدارة الجدولة
            </h2>
            <p class="text-muted mb-0">إدارة جدولة الحصص والحلقات</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('schedules.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                إضافة جدولة جديدة
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي الجدولات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $schedules->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                الجدولات النشطة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $schedules->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                الحلقات المجدولة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $schedules->unique('circle_id')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                الجدولات غير النشطة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $schedules->where('is_active', false)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>
                تصفية الجدولات
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('schedules.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="circle_filter" class="form-label">الحلقة</label>
                        <select class="form-select" id="circle_filter" name="circle_id">
                            <option value="">جميع الحلقات</option>
                            @foreach($circles ?? [] as $circle)
                                <option value="{{ $circle->id }}" {{ request('circle_id') == $circle->id ? 'selected' : '' }}>
                                    {{ $circle->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status_filter" class="form-label">الحالة</label>
                        <select class="form-select" id="status_filter" name="is_active">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشطة</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشطة</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">البحث</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="اسم الجدولة...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>
                                بحث
                            </button>
                            <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedules Cards -->
    @if($schedules->count() > 0)
        <!-- Desktop View -->
        <div class="d-none d-lg-block">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        قائمة الجدولات ({{ $schedules->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>اسم الجدولة</th>
                                    <th>الحلقة</th>
                                    <th>المعلم</th>
                                    <th>فترة الجدولة</th>
                                    <th>اليوم</th>
                                    <th>الوقت</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <div class="font-weight-bold">{{ $schedule->schedule_name }}</div>
                                                    @if($schedule->description)
                                                        <div class="text-muted small">{{ Str::limit($schedule->description, 50) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $schedule->circle->name }}</span>
                                        </td>
                                        <td>{{ $schedule->circle->teacher->name }}</td>
                                        <td>
                                            <div class="text-center">
                                                <div class="small text-muted">من</div>
                                                <div class="fw-bold text-success">{{ \Carbon\Carbon::parse($schedule->start_date)->format('Y/m/d') }}</div>
                                                <div class="small text-muted">إلى</div>
                                                <div class="fw-bold text-danger">{{ \Carbon\Carbon::parse($schedule->end_date)->format('Y/m/d') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $schedule->day_name ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-primary font-weight-bold">{{ $schedule->formatted_time ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            @if($schedule->is_active)
                                                <span class="badge bg-success">نشطة</span>
                                            @else
                                                <span class="badge bg-danger">غير نشطة</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('schedules.show', $schedule) }}" class="btn btn-sm btn-outline-info" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-primary" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجدولة؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet View -->
        <div class="d-lg-none">
            <div class="row">
                @foreach($schedules as $schedule)
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-white">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        {{ $schedule->schedule_name }}
                                    </h6>
                                    @if($schedule->is_active)
                                        <span class="badge bg-success">نشطة</span>
                                    @else
                                        <span class="badge bg-danger">غير نشطة</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Circle Info -->
                                    <div class="col-12 mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-users text-info me-2"></i>
                                            <strong>الحلقة:</strong>
                                            <span class="badge bg-info ms-2">{{ $schedule->circle->name }}</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-chalkboard-teacher text-success me-2"></i>
                                            <strong>المعلم:</strong>
                                            <span class="ms-2">{{ $schedule->circle->teacher->name }}</span>
                                        </div>
                                    </div>

                                    <!-- Date Period -->
                                    <div class="col-12 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body py-2">
                                                <h6 class="card-title mb-2">
                                                    <i class="fas fa-calendar-check text-primary me-1"></i>
                                                    فترة الجدولة
                                                </h6>
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <div class="small text-muted">من</div>
                                                        <div class="fw-bold text-success">{{ \Carbon\Carbon::parse($schedule->start_date)->format('Y/m/d') }}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="small text-muted">إلى</div>
                                                        <div class="fw-bold text-danger">{{ \Carbon\Carbon::parse($schedule->end_date)->format('Y/m/d') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Schedule Details -->
                                    <div class="col-12 mb-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-calendar-day text-secondary me-2"></i>
                                                    <strong>اليوم:</strong>
                                                </div>
                                                <span class="badge bg-secondary">{{ $schedule->day_name ?? 'غير محدد' }}</span>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-clock text-primary me-2"></i>
                                                    <strong>الوقت:</strong>
                                                </div>
                                                <span class="text-primary fw-bold">{{ $schedule->formatted_time ?? 'غير محدد' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    @if($schedule->location)
                                        <div class="col-12 mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-map-marker-alt text-warning me-2"></i>
                                                <strong>المكان:</strong>
                                                <span class="ms-2">{{ $schedule->location }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Description -->
                                    @if($schedule->description)
                                        <div class="col-12 mb-3">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                                <div>
                                                    <strong>الوصف:</strong>
                                                    <p class="mb-0 text-muted small">{{ $schedule->description }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('schedules.show', $schedule) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye me-1"></i>
                                        عرض
                                    </a>
                                    <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit me-1"></i>
                                        تعديل
                                    </a>
                                    <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجدولة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash me-1"></i>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pagination -->
        @if($schedules instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="d-flex justify-content-center mt-4">
                {{ $schedules->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">لا توجد جدولات</h4>
                <p class="text-muted">لم يتم إنشاء أي جدولات بعد</p>
                <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    إضافة جدولة جديدة
                </a>
            </div>
        </div>
    @endif
</div>

<style>
/* Custom responsive styles */
@media (max-width: 768px) {
    .card-header h6 {
        font-size: 0.9rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .badge {
        font-size: 0.7rem;
    }
}

/* Card hover effects */
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

/* Gradient backgrounds */
.bg-gradient-primary {
    background: linear-gradient(45deg, #4e73df, #224abe);
}

/* Custom border colors */
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endsection

