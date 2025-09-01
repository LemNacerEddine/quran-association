@extends('layouts.dashboard')

@section('title', 'إدارة الطلاب المتقدمة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">إدارة الطلاب المتقدمة</h1>
            <p class="mb-0 text-muted">نظام شامل لإدارة ومتابعة تقدم الطلاب</p>
        </div>
        <div>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة طالب جديد
            </a>
            <a href="{{ route('students.bulk-import') }}" class="btn btn-success">
                <i class="fas fa-file-import"></i> استيراد جماعي
            </a>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الطلاب</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_students'] ?? 0 }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">الطلاب النشطين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['active_students'] ?? 0 }}</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">متوسط الحضور</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['average_attendance'] ?? 0 }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">متوسط التقييم</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['average_grade'] ?? 0 }}/10</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">البحث والفلترة المتقدمة</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('students.advanced') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">البحث</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="اسم الطالب أو ولي الأمر...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="circle_id">الحلقة</label>
                            <select class="form-control" id="circle_id" name="circle_id">
                                <option value="">جميع الحلقات</option>
                                @foreach($circles as $circle)
                                    <option value="{{ $circle->id }}" {{ request('circle_id') == $circle->id ? 'selected' : '' }}>
                                        {{ $circle->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="level">المستوى</label>
                            <select class="form-control" id="level" name="level">
                                <option value="">جميع المستويات</option>
                                <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>متقدم</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="attendance_filter">الحضور</label>
                            <select class="form-control" id="attendance_filter" name="attendance_filter">
                                <option value="">جميع الطلاب</option>
                                <option value="excellent" {{ request('attendance_filter') == 'excellent' ? 'selected' : '' }}>ممتاز (90%+)</option>
                                <option value="good" {{ request('attendance_filter') == 'good' ? 'selected' : '' }}>جيد (70-89%)</option>
                                <option value="poor" {{ request('attendance_filter') == 'poor' ? 'selected' : '' }}>ضعيف (أقل من 70%)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="grade_filter">التقييم</label>
                            <select class="form-control" id="grade_filter" name="grade_filter">
                                <option value="">جميع التقييمات</option>
                                <option value="excellent" {{ request('grade_filter') == 'excellent' ? 'selected' : '' }}>ممتاز (9-10)</option>
                                <option value="very_good" {{ request('grade_filter') == 'very_good' ? 'selected' : '' }}>جيد جداً (7-8)</option>
                                <option value="good" {{ request('grade_filter') == 'good' ? 'selected' : '' }}>جيد (5-6)</option>
                                <option value="needs_improvement" {{ request('grade_filter') == 'needs_improvement' ? 'selected' : '' }}>يحتاج تحسين (أقل من 5)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الطلاب المتقدمة</h6>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleView('table')">
                    <i class="fas fa-table"></i> جدول
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleView('cards')">
                    <i class="fas fa-th-large"></i> بطاقات
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Table View -->
            <div id="tableView" class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>الصورة</th>
                            <th>الطالب</th>
                            <th>الحلقة</th>
                            <th>المستوى</th>
                            <th>التقدم</th>
                            <th>الحضور</th>
                            <th>التقييم</th>
                            <th>آخر نشاط</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td class="text-center">
                                @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" 
                                         class="rounded-circle" width="40" height="40" alt="صورة الطالب">
                                @else
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $student->name }}</div>
                                <small class="text-muted">{{ $student->age }} سنة</small><br>
                                <small class="text-muted">ولي الأمر: {{ $student->parent_name }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $student->circle->name ?? 'غير محدد' }}</span>
                                <br><small class="text-muted">{{ $student->circle->teacher->name ?? '' }}</small>
                            </td>
                            <td>
                                @php
                                    $levelColors = [
                                        'beginner' => 'secondary',
                                        'intermediate' => 'warning', 
                                        'advanced' => 'success'
                                    ];
                                    $levelNames = [
                                        'beginner' => 'مبتدئ',
                                        'intermediate' => 'متوسط',
                                        'advanced' => 'متقدم'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $levelColors[$student->level] ?? 'secondary' }}">
                                    {{ $levelNames[$student->level] ?? $student->level }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $student->memorization_progress ?? 0 }}%"
                                         aria-valuenow="{{ $student->memorization_progress ?? 0 }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $student->memorization_progress ?? 0 }}%
                                    </div>
                                </div>
                                <small class="text-muted">{{ $student->current_surah ?? 'لم يبدأ' }}</small>
                            </td>
                            <td class="text-center">
                                @php
                                    $attendance = $student->attendance_rate ?? 0;
                                    $attendanceColor = $attendance >= 90 ? 'success' : ($attendance >= 70 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge badge-{{ $attendanceColor }}">{{ $attendance }}%</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= ($student->average_grade ?? 0) / 2 ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <small class="ml-1">({{ $student->average_grade ?? 0 }}/10)</small>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $student->last_activity ? $student->last_activity->diffForHumans() : 'لا يوجد' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('students.profile', $student) }}" 
                                       class="btn btn-sm btn-info" title="الملف الشخصي">
                                        <i class="fas fa-user"></i>
                                    </a>
                                    <a href="{{ route('students.progress', $student) }}" 
                                       class="btn btn-sm btn-success" title="متابعة التقدم">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" 
                                       class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>لا توجد طلاب مسجلين حالياً</p>
                                    <a href="{{ route('students.create') }}" class="btn btn-primary">
                                        إضافة طالب جديد
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Cards View (Hidden by default) -->
            <div id="cardsView" class="row" style="display: none;">
                @foreach($students as $student)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}" 
                                         class="rounded-circle mr-3" width="50" height="50" alt="صورة الطالب">
                                @else
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mr-3" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $student->name }}</h6>
                                    <small class="text-muted">{{ $student->age }} سنة</small>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <strong>الحلقة:</strong> {{ $student->circle->name ?? 'غير محدد' }}
                            </div>
                            
                            <div class="mb-2">
                                <strong>التقدم:</strong>
                                <div class="progress mt-1" style="height: 15px;">
                                    <div class="progress-bar" style="width: {{ $student->memorization_progress ?? 0 }}%">
                                        {{ $student->memorization_progress ?? 0 }}%
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span><strong>الحضور:</strong> {{ $student->attendance_rate ?? 0 }}%</span>
                                <span><strong>التقييم:</strong> {{ $student->average_grade ?? 0 }}/10</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-group btn-group-sm w-100" role="group">
                                <a href="{{ route('students.profile', $student) }}" class="btn btn-info">
                                    <i class="fas fa-user"></i> الملف
                                </a>
                                <a href="{{ route('students.progress', $student) }}" class="btn btn-success">
                                    <i class="fas fa-chart-line"></i> التقدم
                                </a>
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $students->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleView(viewType) {
    const tableView = document.getElementById('tableView');
    const cardsView = document.getElementById('cardsView');
    
    if (viewType === 'table') {
        tableView.style.display = 'block';
        cardsView.style.display = 'none';
    } else {
        tableView.style.display = 'none';
        cardsView.style.display = 'block';
    }
}
</script>
@endsection

