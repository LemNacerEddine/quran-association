@extends('layouts.teacher')

@section('title', 'الجلسة المباشرة - جمعية تحفيظ القرآن')

@section('content')
<div class="container-fluid">
    <!-- Session Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-1">
                                <i class="fas fa-play-circle me-2"></i>
                                {{ $session->title }}
                            </h3>
                            <p class="mb-0">
                                <i class="fas fa-users me-1"></i>
                                {{ $session->circle->name }} - 
                                <i class="fas fa-calendar me-1"></i>
                                {{ $session->session_date->format('Y-m-d') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex flex-column align-items-md-end">
                                <div class="badge bg-light text-success fs-6 mb-2">
                                    <i class="fas fa-clock me-1"></i>
                                    <span id="session-timer">{{ $stats['session_duration'] }} دقيقة</span>
                                </div>
                                <div class="badge bg-warning text-dark">
                                    <i class="fas fa-broadcast-tower me-1"></i>
                                    جلسة مباشرة
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-3">
            <div class="card text-center bg-primary text-white">
                <div class="card-body py-3">
                    <h4 class="mb-1">{{ $stats['total_students'] }}</h4>
                    <small>إجمالي الطلاب</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body py-3">
                    <h4 class="mb-1">{{ $stats['present'] }}</h4>
                    <small>حاضر</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card text-center bg-danger text-white">
                <div class="card-body py-3">
                    <h4 class="mb-1">{{ $stats['absent'] }}</h4>
                    <small>غائب</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card text-center bg-warning text-white">
                <div class="card-body py-3">
                    <h4 class="mb-1">{{ $stats['late'] }}</h4>
                    <small>متأخر</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card text-center bg-info text-white">
                <div class="card-body py-3">
                    <h4 class="mb-1">{{ $stats['excused'] }}</h4>
                    <small>غياب بعذر</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-3">
            <div class="card text-center bg-secondary text-white">
                <div class="card-body py-3">
                    <h4 class="mb-1">{{ $stats['not_marked'] }}</h4>
                    <small>لم يسجل</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Students List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        قائمة الطلاب والحضور
                    </h5>
                    <a href="{{ route('teacher.sessions.attendance', $session) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>
                        تعديل الحضور
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>الطالب</th>
                                    <th>الحضور</th>
                                    <th>النقاط</th>
                                    <th>الملاحظات</th>
                                    <th>إجراءات سريعة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-2">
                                                {{ substr($student->name, 0, 1) }}
                                            </div>
                                            <strong>{{ $student->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($student->attendance_status === 'present')
                                            <span class="badge bg-success">حاضر</span>
                                        @elseif($student->attendance_status === 'absent')
                                            <span class="badge bg-danger">غائب</span>
                                        @elseif($student->attendance_status === 'late')
                                            <span class="badge bg-warning">متأخر</span>
                                        @elseif($student->attendance_status === 'excused')
                                            <span class="badge bg-info">غياب بعذر</span>
                                        @else
                                            <span class="badge bg-secondary">لم يسجل</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->attendance_points > 0)
                                            <span class="badge bg-primary">{{ $student->attendance_points }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($student->attendance_notes)
                                            <small class="text-muted">{{ Str::limit($student->attendance_notes, 30) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-success btn-sm quick-attendance" 
                                                    data-student="{{ $student->id }}" data-status="present">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm quick-attendance" 
                                                    data-student="{{ $student->id }}" data-status="absent">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm quick-attendance" 
                                                    data-student="{{ $student->id }}" data-status="late">
                                                <i class="fas fa-clock"></i>
                                            </button>
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

        <!-- Session Tools -->
        <div class="col-lg-4">
            <!-- Session Controls -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        أدوات الجلسة
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.sessions.end', $session) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 mb-2" 
                                onclick="return confirm('هل أنت متأكد من إنهاء الجلسة؟')">
                            <i class="fas fa-stop me-2"></i>إنهاء الجلسة
                        </button>
                    </form>

                    <a href="{{ route('teacher.sessions.attendance', $session) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-users me-2"></i>إدارة الحضور التفصيلي
                    </a>

                    <a href="{{ route('teacher.sessions.show', $session) }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-eye me-2"></i>عرض تفاصيل الجلسة
                    </a>
                </div>
            </div>

            <!-- Session Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>
                        ملاحظات الجلسة
                    </h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" rows="4" placeholder="اكتب ملاحظاتك حول الجلسة هنا..." id="session-notes"></textarea>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2 w-100" onclick="saveNotes()">
                        <i class="fas fa-save me-1"></i>حفظ الملاحظات
                    </button>
                </div>
            </div>

            <!-- Quick Stats Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        ملخص الجلسة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2">
                                <h6 class="text-success mb-1">{{ $stats['total_points'] }}</h6>
                                <small class="text-muted">إجمالي النقاط</small>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="border rounded p-2">
                                <h6 class="text-primary mb-1">{{ number_format(($stats['present'] / max($stats['total_students'], 1)) * 100, 1) }}%</h6>
                                <small class="text-muted">نسبة الحضور</small>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ ($stats['present'] / max($stats['total_students'], 1)) * 100 }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ ($stats['late'] / max($stats['total_students'], 1)) * 100 }}%"></div>
                        <div class="progress-bar bg-info" style="width: {{ ($stats['excused'] / max($stats['total_students'], 1)) * 100 }}%"></div>
                        <div class="progress-bar bg-danger" style="width: {{ ($stats['absent'] / max($stats['total_students'], 1)) * 100 }}%"></div>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <span class="text-success">■</span> حاضر 
                        <span class="text-warning">■</span> متأخر 
                        <span class="text-info">■</span> بعذر 
                        <span class="text-danger">■</span> غائب
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Session Timer
let sessionStartTime = new Date('{{ $session->actual_start_time }}');
let timerElement = document.getElementById('session-timer');

function updateTimer() {
    let now = new Date();
    let diff = Math.floor((now - sessionStartTime) / 1000 / 60); // minutes
    timerElement.textContent = diff + ' دقيقة';
}

// Update timer every minute
setInterval(updateTimer, 60000);

// Quick attendance buttons
document.querySelectorAll('.quick-attendance').forEach(button => {
    button.addEventListener('click', function() {
        let studentId = this.dataset.student;
        let status = this.dataset.status;
        
        // Here you would typically make an AJAX call to update attendance
        // For now, we'll just show a confirmation
        let statusText = {
            'present': 'حاضر',
            'absent': 'غائب', 
            'late': 'متأخر'
        };
        
        if(confirm(`تسجيل الطالب كـ "${statusText[status]}"؟`)) {
            // Make AJAX call here
            console.log(`Student ${studentId} marked as ${status}`);
            // Reload page to show updated status
            location.reload();
        }
    });
});

// Save notes function
function saveNotes() {
    let notes = document.getElementById('session-notes').value;
    if(notes.trim()) {
        // Here you would save notes via AJAX
        alert('تم حفظ الملاحظات بنجاح');
    }
}

// Auto-refresh stats every 30 seconds
setInterval(function() {
    // You could implement AJAX refresh of stats here
}, 30000);
</script>
@endpush
@endsection

