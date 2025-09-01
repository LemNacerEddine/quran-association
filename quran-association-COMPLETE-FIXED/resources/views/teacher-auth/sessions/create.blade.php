@extends('layouts.teacher')

@section('title', 'إنشاء جلسة جديدة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary mb-1">إنشاء جلسة جديدة</h2>
                    <p class="text-muted mb-0">إنشاء جلسة جديدة لإحدى الحلقات</p>
                </div>
                <div>
                    <a href="{{ route('teacher.sessions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للجلسات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        بيانات الجلسة الجديدة
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.sessions.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="circle_id" class="form-label">الحلقة <span class="text-danger">*</span></label>
                                <select class="form-select @error('circle_id') is-invalid @enderror" id="circle_id" name="circle_id" required>
                                    <option value="">اختر الحلقة</option>
                                    @foreach($circles as $circle)
                                    <option value="{{ $circle->id }}" {{ old('circle_id') == $circle->id ? 'selected' : '' }}>
                                        {{ $circle->name }} ({{ $circle->students->count() }} طالب)
                                    </option>
                                    @endforeach
                                </select>
                                @error('circle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">عنوان الجلسة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="مثال: مراجعة سورة البقرة" required>
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="session_date" class="form-label">تاريخ الجلسة <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('session_date') is-invalid @enderror" 
                                       id="session_date" name="session_date" value="{{ old('session_date', date('Y-m-d')) }}" required>
                                @error('session_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="start_time" class="form-label">وقت البداية <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time', '08:00') }}" required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="end_time" class="form-label">وقت النهاية <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" value="{{ old('end_time', '09:30') }}" required>
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">وصف الجلسة</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="وصف مختصر لمحتوى الجلسة والأهداف">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('teacher.sessions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>إنشاء الجلسة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate title based on circle selection
    const circleSelect = document.getElementById('circle_id');
    const titleInput = document.getElementById('title');
    const dateInput = document.getElementById('session_date');
    
    function updateTitle() {
        const selectedOption = circleSelect.options[circleSelect.selectedIndex];
        const date = dateInput.value;
        
        if (selectedOption.value && date) {
            const circleName = selectedOption.text.split(' (')[0];
            const formattedDate = new Date(date).toLocaleDateString('ar-SA');
            titleInput.value = `جلسة ${circleName} - ${formattedDate}`;
        }
    }
    
    circleSelect.addEventListener('change', updateTitle);
    dateInput.addEventListener('change', updateTitle);
    
    // Validate time inputs
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    function validateTimes() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (startTime && endTime && startTime >= endTime) {
            endTimeInput.setCustomValidity('وقت النهاية يجب أن يكون بعد وقت البداية');
        } else {
            endTimeInput.setCustomValidity('');
        }
    }
    
    startTimeInput.addEventListener('change', validateTimes);
    endTimeInput.addEventListener('change', validateTimes);
});
</script>
@endsection
@endsection

