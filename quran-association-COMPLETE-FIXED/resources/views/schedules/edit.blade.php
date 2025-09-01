@extends('layouts.app')

@section('title', 'تعديل الجدولة')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-calendar-edit me-2"></i>
                        تعديل الجدولة
                    </h2>
                    <p class="text-muted mb-0">تعديل جدولة: {{ $schedule->schedule_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('schedules.show', $schedule) }}" class="btn btn-info">
                        <i class="fas fa-eye me-2"></i>
                        عرض التفاصيل
                    </a>
                    <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('schedules.update', $schedule) }}" method="POST" id="scheduleForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Main Form Column -->
                    <div class="col-lg-8">
                        <!-- Basic Information Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    المعلومات الأساسية
                                </h5>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger mb-4">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>خطأ في البيانات!</h6>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success mb-4">
                                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    </div>
                                @endif

                                <!-- Circle Selection -->
                                <div class="mb-4">
                                    <label for="circle_id" class="form-label">
                                        <i class="fas fa-users text-primary me-1"></i>
                                        اختيار الحلقة <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('circle_id') is-invalid @enderror" 
                                            id="circle_id" name="circle_id" required onchange="loadCircleInfo()">
                                        <option value="">-- اختر الحلقة --</option>
                                        @foreach($circles as $circle)
                                            <option value="{{ $circle->id }}" 
                                                    data-name="{{ $circle->name }}"
                                                    data-teacher="{{ $circle->teacher->name ?? 'غير محدد' }}"
                                                    data-location="{{ $circle->location }}"
                                                    data-start-time="{{ $circle->start_time }}"
                                                    data-end-time="{{ $circle->end_time }}"
                                                    data-days="{{ $circle->schedule_days }}"
                                                    data-level="{{ $circle->level }}"
                                                    data-max-students="{{ $circle->max_students }}"
                                                    {{ $schedule->circle_id == $circle->id ? 'selected' : '' }}>
                                                {{ $circle->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('circle_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Schedule Name -->
                                <div class="mb-4">
                                    <label for="schedule_name" class="form-label">
                                        <i class="fas fa-tag text-primary me-1"></i>
                                        اسم الجدولة <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('schedule_name') is-invalid @enderror" 
                                           id="schedule_name" 
                                           name="schedule_name" 
                                           value="{{ old('schedule_name', $schedule->schedule_name) }}" 
                                           required>
                                    @error('schedule_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date Range -->
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="start_date" class="form-label">
                                            <i class="fas fa-calendar-alt text-success me-1"></i>
                                            تاريخ البداية <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('start_date') is-invalid @enderror" 
                                               id="start_date" 
                                               name="start_date" 
                                               value="{{ old('start_date', $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('Y-m-d') : '') }}" 
                                               required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="end_date" class="form-label">
                                            <i class="fas fa-calendar-alt text-danger me-1"></i>
                                            تاريخ النهاية <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               id="end_date" 
                                               name="end_date" 
                                               value="{{ old('end_date', $schedule->end_date ? \Carbon\Carbon::parse($schedule->end_date)->format('Y-m-d') : '') }}" 
                                               required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>



                                <!-- Location -->
                                <div class="mb-4">
                                    <label for="location" class="form-label">
                                        <i class="fas fa-map-marker-alt text-warning me-1"></i>
                                        الموقع
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('location') is-invalid @enderror" 
                                           id="location" 
                                           name="location" 
                                           value="{{ old('location', $schedule->location) }}" 
                                           placeholder="موقع إقامة الجلسات">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Max Students -->
                                <div class="mb-4">
                                    <label for="max_students" class="form-label">
                                        <i class="fas fa-users text-info me-1"></i>
                                        الحد الأقصى للطلاب
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('max_students') is-invalid @enderror" 
                                           id="max_students" 
                                           name="max_students" 
                                           value="{{ old('max_students', $schedule->max_students) }}" 
                                           min="1" max="50" 
                                           placeholder="عدد الطلاب الأقصى">
                                    @error('max_students')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left text-info me-1"></i>
                                        وصف الجدولة
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="وصف عام للجدولة (اختياري)">{{ old('description', $schedule->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="mb-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <i class="fas fa-toggle-on text-success me-1"></i>
                                            الجدولة نشطة
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="auto_create_sessions" 
                                               name="auto_create_sessions" 
                                               value="1" 
                                               {{ old('auto_create_sessions', $schedule->auto_create_sessions) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_create_sessions">
                                            <i class="fas fa-magic text-primary me-1"></i>
                                            إنشاء الجلسات تلقائياً
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="requires_attendance" 
                                               name="requires_attendance" 
                                               value="1" 
                                               {{ old('requires_attendance', $schedule->requires_attendance) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_attendance">
                                            <i class="fas fa-check-circle text-warning me-1"></i>
                                            يتطلب تسجيل حضور
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Session Management Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-magic me-2"></i>
                                    إدارة الجلسات
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-success w-100 mb-3" onclick="createSessionsAutoWithProgress()">
                                            <i class="fas fa-magic me-2"></i>
                                            إنشاء الجلسات تلقائياً للمدة الكاملة
                                        </button>
                                        
                                        <!-- Progress Bar (hidden by default) -->
                                        <div id="progressContainer" class="mb-3" style="display: none;">
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
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    سيتم إنشاء جميع الجلسات تلقائياً بناءً على أيام الحلقة المحددة وفترة الجدولة الكاملة
                                </small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                حفظ التعديلات
                            </button>
                        </div>
                    </div>

                    <!-- Circle Info Sidebar -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm" id="circleInfoCard">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    معلومات الحلقة
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="circleInfo">
                                    <!-- Circle information will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Statistics -->
                        <div class="card shadow-sm mt-4">
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
                                            <h4 class="text-primary mb-1">{{ $schedule->sessions()->count() }}</h4>
                                            <small class="text-muted">إجمالي الجلسات</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-1">{{ $schedule->sessions()->where('status', 'completed')->count() }}</h4>
                                        <small class="text-muted">جلسات مكتملة</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadCircleInfo() {
    const select = document.getElementById('circle_id');
    const selectedOption = select.options[select.selectedIndex];
    const circleInfo = document.getElementById('circleInfo');
    const scheduleNameInput = document.getElementById('schedule_name');
    
    if (selectedOption.value) {
        // Auto-fill schedule name if empty
        if (!scheduleNameInput.value || scheduleNameInput.value.startsWith('جدولة ')) {
            scheduleNameInput.value = `جدولة ${selectedOption.dataset.name}`;
        }
        
        // Load circle information
        const days = selectedOption.dataset.days || 'غير محدد';
        const startTime = selectedOption.dataset.startTime || 'غير محدد';
        const endTime = selectedOption.dataset.endTime || 'غير محدد';
        
        circleInfo.innerHTML = `
            <div class="mb-3">
                <h6 class="text-primary mb-2">
                    <i class="fas fa-chalkboard-teacher me-1"></i>
                    المعلم
                </h6>
                <p class="mb-0 text-muted">${selectedOption.dataset.teacher}</p>
            </div>
            
            <div class="mb-3">
                <h6 class="text-primary mb-2">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    المكان
                </h6>
                <p class="mb-0 text-muted">${selectedOption.dataset.location}</p>
            </div>
            
            <div class="mb-3">
                <h6 class="text-primary mb-2">
                    <i class="fas fa-calendar-week me-1"></i>
                    أيام الحلقة
                </h6>
                <p class="mb-0 text-muted">${days}</p>
            </div>
            
            <div class="mb-3">
                <h6 class="text-primary mb-2">
                    <i class="fas fa-clock me-1"></i>
                    التوقيت
                </h6>
                <p class="mb-0 text-muted">${startTime} - ${endTime}</p>
            </div>
            
            <div class="mb-3">
                <h6 class="text-primary mb-2">
                    <i class="fas fa-layer-group me-1"></i>
                    المستوى
                </h6>
                <span class="badge bg-primary">${selectedOption.dataset.level}</span>
            </div>
            
            <div class="mb-0">
                <h6 class="text-primary mb-2">
                    <i class="fas fa-users me-1"></i>
                    الحد الأقصى للطلاب
                </h6>
                <span class="badge bg-success">${selectedOption.dataset.maxStudents} طالب</span>
            </div>
        `;
    }
}

function createSessionsAutoWithProgress() {
    if (confirm('هل تريد إنشاء جميع الجلسات تلقائياً للمدة الكاملة المحددة في الجدولة؟')) {
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
        { percent: 10, message: 'تحضير البيانات...' },
        { percent: 25, message: 'تحليل الجدولة...' },
        { percent: 40, message: 'إنشاء الجلسات الأسبوعية...' },
        { percent: 60, message: 'إنشاء الجلسات الشهرية...' },
        { percent: 80, message: 'التحقق من التعارضات...' },
        { percent: 95, message: 'حفظ البيانات...' },
        { percent: 100, message: 'تم الانتهاء بنجاح!' }
    ];
    
    let currentStep = 0;
    
    function nextStep() {
        if (currentStep < progressSteps.length) {
            const step = progressSteps[currentStep];
            updateProgress(step.percent, step.message);
            currentStep++;
            
            if (currentStep < progressSteps.length) {
                setTimeout(nextStep, 800); // Simulate processing time
            } else {
                // Actually create the sessions
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
    
    // Change color based on progress
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
    createButton.innerHTML = '<i class="fas fa-magic me-2"></i>إنشاء الجلسات تلقائياً للمدة الكاملة';
    
    setTimeout(() => {
        progressContainer.style.display = 'none';
    }, 3000);
}

// Legacy functions (kept for compatibility)
function createWeeklySessions() {
    createSessionsAutoWithProgress();
}

function createMonthlySessions() {
    createSessionsAutoWithProgress();
}

// Load circle info on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCircleInfo();
    
    // Update end date minimum when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').setAttribute('min', this.value);
    });
});
</script>
@endsection

