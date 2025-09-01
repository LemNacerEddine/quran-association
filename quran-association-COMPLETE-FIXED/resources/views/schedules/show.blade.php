@extends('layouts.app')

@section('title', 'تفاصيل الجدولة')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-calendar-alt me-2"></i>
                        تفاصيل الجدولة
                    </h2>
                    <p class="text-muted mb-0">{{ $schedule->schedule_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Schedule Information Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                معلومات الجدولة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-tag me-1"></i>
                                        اسم الجدولة
                                    </h6>
                                    <p class="mb-0 text-muted">{{ $schedule->schedule_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-users me-1"></i>
                                        الحلقة
                                    </h6>
                                    <p class="mb-0 text-muted">{{ $schedule->circle->name ?? 'غير محدد' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        تاريخ البداية
                                    </h6>
                                    <p class="mb-0 text-muted">{{ $schedule->start_date }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        تاريخ النهاية
                                    </h6>
                                    <p class="mb-0 text-muted">{{ $schedule->end_date }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-align-left me-1"></i>
                                        الوصف
                                    </h6>
                                    <p class="mb-0 text-muted">{{ $schedule->description ?? 'لا يوجد وصف' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-toggle-on me-1"></i>
                                        الحالة
                                    </h6>
                                    @if($schedule->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-calendar-plus me-1"></i>
                                        تاريخ الإنشاء
                                    </h6>
                                    <p class="mb-0 text-muted">{{ $schedule->created_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sessions List Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                قائمة الجلسات
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($schedule->sessions && $schedule->sessions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>التاريخ</th>
                                                <th>الوقت</th>
                                                <th>الحالة</th>
                                                <th>الحضور</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schedule->sessions->take(10) as $session)
                                                <tr>
                                                    <td>{{ $session->session_date }}</td>
                                                    <td>{{ $session->start_time }} - {{ $session->end_time }}</td>
                                                    <td>
                                                        @switch($session->status)
                                                            @case('scheduled')
                                                                <span class="badge bg-primary">مجدولة</span>
                                                                @break
                                                            @case('completed')
                                                                <span class="badge bg-success">مكتملة</span>
                                                                @break
                                                            @case('cancelled')
                                                                <span class="badge bg-danger">ملغية</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">غير محدد</span>
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ $session->attendances ? $session->attendances->count() : 0 }} طالب
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('sessions.show', $session) }}" class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('sessions.edit', $session) }}" class="btn btn-outline-warning btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($schedule->sessions->count() > 10)
                                    <div class="text-center mt-3">
                                        <a href="{{ route('sessions.index', ['schedule_id' => $schedule->id]) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-list me-2"></i>
                                            عرض جميع الجلسات ({{ $schedule->sessions->count() }})
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">لا توجد جلسات</h5>
                                    <p class="text-muted">لم يتم إنشاء أي جلسات لهذه الجدولة بعد</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-success" onclick="createSessionsAutoWithProgress()">
                                            <i class="fas fa-magic me-2"></i>
                                            إنشاء الجلسات تلقائياً
                                        </button>
                                    </div>
                                    
                                    <!-- Progress Bar (hidden by default) -->
                                    <div id="progressContainer" class="mt-3" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">جاري إنشاء الجلسات...</small>
                                            <small id="progressText" class="text-primary">0%</small>
                                        </div>
                                        <div class="progress">
                                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small id="progressStatus" class="text-muted">بدء العملية...</small>
                                    </div>
                                </div>
                            @endif
                            
                            @if($sessions->count() > 0)
                                <!-- Sessions exist - show recreate option -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-primary mb-0">
                                        <i class="fas fa-list me-1"></i>
                                        الجلسات المُنشأة ({{ $sessions->count() }})
                                    </h6>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="recreateSessions()">
                                        <i class="fas fa-redo me-2"></i>
                                        إعادة إنشاء الجلسات
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Circle Information Card -->
                    @if($schedule->circle)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                معلومات الحلقة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-tag me-1"></i>
                                    اسم الحلقة
                                </h6>
                                <p class="mb-0 text-muted">{{ $schedule->circle->name }}</p>
                            </div>
                            
                            @if($schedule->circle->teacher)
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>
                                    المعلم
                                </h6>
                                <p class="mb-0 text-muted">{{ $schedule->circle->teacher->name }}</p>
                            </div>
                            @endif
                            
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    المكان
                                </h6>
                                <p class="mb-0 text-muted">{{ $schedule->circle->location ?? 'غير محدد' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-clock me-1"></i>
                                    التوقيت
                                </h6>
                                <p class="mb-0 text-muted">{{ $schedule->circle->start_time }} - {{ $schedule->circle->end_time }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-calendar-week me-1"></i>
                                    أيام الحلقة
                                </h6>
                                <p class="mb-0 text-muted">{{ $schedule->circle->schedule_days ?? 'غير محدد' }}</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-layer-group me-1"></i>
                                    المستوى
                                </h6>
                                <span class="badge bg-primary">{{ $schedule->circle->level ?? 'غير محدد' }}</span>
                            </div>
                            
                            <div class="mb-0">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-users me-1"></i>
                                    عدد الطلاب
                                </h6>
                                <span class="badge bg-success">{{ $schedule->circle->students ? $schedule->circle->students->count() : 0 }} طالب</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Statistics Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                إحصائيات الجدولة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-1">{{ $schedule->sessions ? $schedule->sessions->count() : 0 }}</h4>
                                        <small class="text-muted">إجمالي الجلسات</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success mb-1">{{ $schedule->sessions ? $schedule->sessions->where('status', 'completed')->count() : 0 }}</h4>
                                    <small class="text-muted">جلسات مكتملة</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-info mb-1">{{ $schedule->sessions ? $schedule->sessions->where('status', 'scheduled')->count() : 0 }}</h4>
                                        <small class="text-muted">جلسات مجدولة</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-danger mb-1">{{ $schedule->sessions ? $schedule->sessions->where('status', 'cancelled')->count() : 0 }}</h4>
                                    <small class="text-muted">جلسات ملغية</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                إجراءات سريعة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>
                                    تعديل الجدولة
                                </a>
                                @if($schedule->circle)
                                <a href="{{ route('circles.show', $schedule->circle) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-users me-2"></i>
                                    عرض الحلقة
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createSessionsAutoWithProgress() {
    if (confirm('هل تريد إنشاء الجلسات تلقائياً لهذه الجدولة؟')) {
        // Show progress container
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressStatus = document.getElementById('progressStatus');
        const createButton = document.querySelector('button[onclick="createSessionsAutoWithProgress()"]');
        
        // Show progress and disable button
        progressContainer.style.display = 'block';
        createButton.disabled = true;
        createButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإنشاء...';
        
        // Reset progress
        updateProgress(0, 'بدء العملية...');
        
        // Start session creation with progress tracking
        createSessionsWithProgress();
    }
}

function createSessionsWithProgress() {
    const progressSteps = [
        { percent: 15, message: 'تحضير البيانات...' },
        { percent: 30, message: 'تحليل الجدولة...' },
        { percent: 50, message: 'إنشاء الجلسات...' },
        { percent: 75, message: 'التحقق من التعارضات...' },
        { percent: 90, message: 'حفظ البيانات...' },
        { percent: 100, message: 'تم الانتهاء بنجاح!' }
    ];
    
    let currentStep = 0;
    
    function nextStep() {
        if (currentStep < progressSteps.length) {
            const step = progressSteps[currentStep];
            updateProgress(step.percent, step.message);
            currentStep++;
            
            if (currentStep < progressSteps.length) {
                setTimeout(nextStep, 600);
            } else {
                performSessionCreation();
            }
        }
    }
    
    nextStep();
}

function updateProgress(percent, message) {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressStatus = document.getElementById('progressStatus');
    
    progressBar.style.width = percent + '%';
    progressBar.setAttribute('aria-valuenow', percent);
    progressText.textContent = percent + '%';
    progressStatus.textContent = message;
    
    if (percent < 50) {
        progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-info';
    } else if (percent < 90) {
        progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-warning';
    } else {
        progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-success';
    }
}

function performSessionCreation() {
    fetch(`/schedules/{{ $schedule->id }}/create-sessions-auto`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateProgress(100, 'تم إنشاء ' + (data.count || 'جميع') + ' الجلسات بنجاح!');
            setTimeout(() => {
                alert(data.message);
                location.reload();
            }, 1000);
        } else {
            updateProgress(0, 'حدث خطأ: ' + data.message);
            resetButton();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        updateProgress(0, 'حدث خطأ أثناء إنشاء الجلسات');
        resetButton();
    });
}

function resetButton() {
    const createButton = document.querySelector('button[onclick="createSessionsAutoWithProgress()"]');
    const progressContainer = document.getElementById('progressContainer');
    
    createButton.disabled = false;
    createButton.innerHTML = '<i class="fas fa-magic me-2"></i>إنشاء الجلسات تلقائياً';
    
    setTimeout(() => {
        progressContainer.style.display = 'none';
    }, 3000);
}

function recreateSessions() {
    if (confirm('هل تريد حذف الجلسات الموجودة وإنشاء جلسات جديدة؟\n\nملاحظة: سيتم الاحتفاظ بالجلسات التي تم تسجيل الحضور فيها.')) {
        fetch(`/schedules/{{ $schedule->id }}/recreate-sessions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إعادة إنشاء الجلسات');
        });
    }
}

// Legacy functions (kept for compatibility)
function createSessionsAuto() {
    createSessionsAutoWithProgress();
}

</script>
@endsection

