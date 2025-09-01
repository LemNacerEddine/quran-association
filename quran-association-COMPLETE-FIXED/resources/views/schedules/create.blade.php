@extends('layouts.app')

@section('title', 'إضافة جدولة جديدة')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-calendar-plus me-2"></i>
                        إضافة جدولة جديدة
                    </h2>
                    <p class="text-muted mb-0">قم بإنشاء جدولة جديدة للحلقات</p>
                </div>
                <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة للقائمة
                </a>
            </div>

            <!-- Form -->
            <form action="{{ route('schedules.store') }}" method="POST" id="scheduleForm">
                @csrf
                
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
                                                    data-days="{{ $circle->days }}"
                                                    data-level="{{ $circle->level }}"
                                                    data-max-students="{{ $circle->max_students }}"
                                                    {{ old('circle_id') == $circle->id ? 'selected' : '' }}>
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
                                           class="form-select @error('schedule_name') is-invalid @enderror" 
                                           id="schedule_name" 
                                           name="schedule_name" 
                                           value="{{ old('schedule_name') }}" 
                                           required 
                                           readonly
                                           placeholder="سيتم ملؤه تلقائياً عند اختيار الحلقة">
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
                                               value="{{ old('start_date') }}" 
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
                                               value="{{ old('end_date') }}" 
                                               required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
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
                                              placeholder="وصف عام للجدولة (اختياري)">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Auto Create Sessions -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="auto_create_sessions" 
                                               name="auto_create_sessions" 
                                               value="1" 
                                               {{ old('auto_create_sessions') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_create_sessions">
                                            <i class="fas fa-magic text-warning me-1"></i>
                                            إنشاء الجلسات تلقائياً
                                        </label>
                                        <small class="form-text text-muted d-block">
                                            سيتم إنشاء جلسات أسبوعية تلقائياً بناءً على أيام الحلقة المحددة
                                        </small>
                                    </div>
                                </div>
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
                                حفظ الجدولة
                            </button>
                        </div>
                    </div>

                    <!-- Circle Info Sidebar -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm" id="circleInfoCard" style="display: none;">
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
    const circleInfoCard = document.getElementById('circleInfoCard');
    const circleInfo = document.getElementById('circleInfo');
    const scheduleNameInput = document.getElementById('schedule_name');
    
    if (selectedOption.value) {
        // Show circle info card
        circleInfoCard.style.display = 'block';
        
        // Auto-fill schedule name
        scheduleNameInput.value = `جدولة ${selectedOption.dataset.name}`;
        
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
    } else {
        // Hide circle info card
        circleInfoCard.style.display = 'none';
        scheduleNameInput.value = '';
    }
}

// Set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').setAttribute('min', today);
    
    // Update end date minimum when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').setAttribute('min', this.value);
    });
});
</script>
@endsection

