@extends('layouts.dashboard')

@section('title', 'إدارة الجدولة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">إدارة الجدولة</h1>
            <p class="text-muted">إدارة جدولة الحصص والحلقات</p>
        </div>
        <div>
            <a href="{{ route('schedules.weekly') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-week"></i> العرض الأسبوعي
            </a>
            <div class="btn-group me-2" role="group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-magic"></i> إنشاء جلسات تلقائية
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="showAutoCreateInfo()">
                        <i class="fas fa-info-circle"></i> كيفية إنشاء الجلسات
                    </a></li>
                </ul>
            </div>
            <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة جدولة جديدة
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي الجدولات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $schedules->total() }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $schedules->where('is_active', true)->count() }}
                            </div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $circles->count() }}</div>
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
                                المعلمون
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $teachers->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة الجدولات</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('schedules.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="circle_id" class="form-label">الحلقة</label>
                        <select name="circle_id" id="circle_id" class="form-select">
                            <option value="">جميع الحلقات</option>
                            @foreach($circles as $circle)
                                <option value="{{ $circle->id }}" {{ request('circle_id') == $circle->id ? 'selected' : '' }}>
                                    {{ $circle->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="day_of_week" class="form-label">يوم الأسبوع</label>
                        <select name="day_of_week" id="day_of_week" class="form-select">
                            <option value="">جميع الأيام</option>
                            <option value="sunday" {{ request('day_of_week') == 'sunday' ? 'selected' : '' }}>الأحد</option>
                            <option value="monday" {{ request('day_of_week') == 'monday' ? 'selected' : '' }}>الاثنين</option>
                            <option value="tuesday" {{ request('day_of_week') == 'tuesday' ? 'selected' : '' }}>الثلاثاء</option>
                            <option value="wednesday" {{ request('day_of_week') == 'wednesday' ? 'selected' : '' }}>الأربعاء</option>
                            <option value="thursday" {{ request('day_of_week') == 'thursday' ? 'selected' : '' }}>الخميس</option>
                            <option value="friday" {{ request('day_of_week') == 'friday' ? 'selected' : '' }}>الجمعة</option>
                            <option value="saturday" {{ request('day_of_week') == 'saturday' ? 'selected' : '' }}>السبت</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="session_type" class="form-label">نوع الحصة</label>
                        <select name="session_type" id="session_type" class="form-select">
                            <option value="">جميع الأنواع</option>
                            <option value="morning" {{ request('session_type') == 'morning' ? 'selected' : '' }}>صباحية</option>
                            <option value="afternoon" {{ request('session_type') == 'afternoon' ? 'selected' : '' }}>ظهيرة</option>
                            <option value="evening" {{ request('session_type') == 'evening' ? 'selected' : '' }}>مسائية</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="is_active" class="form-label">الحالة</label>
                        <select name="is_active" id="is_active" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشطة</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشطة</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Schedules Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الجدولات</h6>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="fas fa-check-square"></i> تحديد الكل
                </button>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        إجراءات مجمعة
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('activate')">تفعيل المحدد</a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">إلغاء تفعيل المحدد</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">حذف المحدد</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($schedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                </th>
                                <th>اسم الجدولة</th>
                                <th>الحلقة</th>
                                <th>المعلم</th>
                                <th>فترة الجدولة</th>
                                <th>اليوم</th>
                                <th>الوقت</th>
                                <th>النوع</th>
                                <th>المكان</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="schedule_ids[]" value="{{ $schedule->id }}" class="schedule-checkbox">
                                    </td>
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
                                        <span class="badge bg-secondary">{{ $schedule->day_name }}</span>
                                    </td>
                                    <td>
                                        <span class="text-primary font-weight-bold">{{ $schedule->formatted_time }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $schedule->session_type === 'morning' ? 'warning' : ($schedule->session_type === 'afternoon' ? 'info' : 'dark') }}">
                                            {{ $schedule->session_type_name }}
                                        </span>
                                    </td>
                                    <td>{{ $schedule->location ?? 'غير محدد' }}</td>
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
                                            <form action="{{ route('schedules.toggle-status', $schedule) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $schedule->is_active ? 'warning' : 'success' }}" title="{{ $schedule->is_active ? 'إلغاء تفعيل' : 'تفعيل' }}">
                                                    <i class="fas fa-{{ $schedule->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
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

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $schedules->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">لا توجد جدولات</h5>
                    <p class="text-muted">لم يتم العثور على أي جدولات. قم بإضافة جدولة جديدة.</p>
                    <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة جدولة جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Action Form -->
<form id="bulkActionForm" method="POST" action="{{ route('schedules.bulk-action') }}" style="display: none;">
    @csrf
    <input type="hidden" name="action" id="bulkAction">
    <input type="hidden" name="schedule_ids" id="bulkScheduleIds">
</form>

@endsection

@push('scripts')
<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('.schedule-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    selectAllCheckbox.checked = true;
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.schedule-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

function showAutoCreateInfo() {
    alert('لإنشاء الجلسات تلقائياً:\n\n' +
          '1. اذهب إلى تفاصيل الجدولة المطلوبة\n' +
          '2. اضغط على زر "إنشاء الجلسات تلقائياً"\n' +
          '3. أو استخدم زر "إعادة إنشاء الجلسات" لتحديث الجلسات الموجودة\n\n' +
          'سيتم إنشاء الجلسات حسب أيام وأوقات الحلقة المحددة في الجدولة.');
}

// Legacy functions (kept for compatibility)
function createWeeklySessions() {
    showAutoCreateInfo();
}

function createMonthlySessions() {
    showAutoCreateInfo();
}

function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.schedule-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('يرجى تحديد جدولة واحدة على الأقل');
        return;
    }
    
    const scheduleIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    let confirmMessage = '';
    switch(action) {
        case 'activate':
            confirmMessage = 'هل أنت متأكد من تفعيل الجدولات المحددة؟';
            break;
        case 'deactivate':
            confirmMessage = 'هل أنت متأكد من إلغاء تفعيل الجدولات المحددة؟';
            break;
        case 'delete':
            confirmMessage = 'هل أنت متأكد من حذف الجدولات المحددة؟ هذا الإجراء لا يمكن التراجع عنه.';
            break;
    }
    
    if (confirm(confirmMessage)) {
        document.getElementById('bulkAction').value = action;
        document.getElementById('bulkScheduleIds').value = JSON.stringify(scheduleIds);
        document.getElementById('bulkActionForm').submit();
    }
}

// Update select all checkbox when individual checkboxes change
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.schedule-checkbox');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.schedule-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === checkboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
        });
    });
});
</script>
@endpush

