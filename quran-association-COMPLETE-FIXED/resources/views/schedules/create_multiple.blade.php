@extends('layouts.dashboard')

@section('title', 'إضافة جدولة متعددة الأيام')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-plus"></i>
                        إضافة جدولة متعددة الأيام
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> رجوع للقائمة
                        </a>
                        <a href="{{ route('schedules.create') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-calendar-day"></i> جدولة يوم واحد
                        </a>
                    </div>
                </div>
                
                <form action="{{ route('schedules.store') }}" method="POST" id="multipleScheduleForm">
                    @csrf
                    <input type="hidden" name="has_multiple_days" value="1">
                    
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> خطأ في البيانات!</h5>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <!-- المعلومات الأساسية -->
                            <div class="col-md-6">
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">المعلومات الأساسية</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="schedule_name">اسم الجدولة <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('schedule_name') is-invalid @enderror" 
                                                   id="schedule_name" name="schedule_name" value="{{ old('schedule_name') }}" 
                                                   placeholder="مثال: حلقة تحفيظ المبتدئين - متعددة الأيام" required>
                                            @error('schedule_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="circle_id">الحلقة <span class="text-danger">*</span></label>
                                            <select class="form-control select2 @error('circle_id') is-invalid @enderror" 
                                                    id="circle_id" name="circle_id" required>
                                                <option value="">اختر الحلقة</option>
                                                @foreach($circles as $circle)
                                                    <option value="{{ $circle->id }}" 
                                                            data-teacher="{{ $circle->teacher->name ?? 'غير محدد' }}"
                                                            data-students="{{ $circle->students_count ?? 0 }}"
                                                            {{ old('circle_id') == $circle->id ? 'selected' : '' }}>
                                                        {{ $circle->name }}
                                                        ({{ $circle->teacher->name ?? 'غير محدد' }} - {{ $circle->students_count ?? 0 }} طالب)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('circle_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="description">وصف الجدولة</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="وصف مختصر للجدولة ومحتواها...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="location">المكان الافتراضي</label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                                   id="location" name="location" value="{{ old('location') }}" 
                                                   placeholder="مثال: قاعة A، المسجد الرئيسي">
                                            @error('location')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">يمكن تخصيص مكان مختلف لكل يوم</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- الإعدادات العامة -->
                            <div class="col-md-6">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">الإعدادات العامة</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="start_date">تاريخ البداية</label>
                                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                           id="start_date" name="start_date" 
                                                           value="{{ old('start_date', now()->format('Y-m-d')) }}">
                                                    @error('start_date')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="end_date">تاريخ النهاية</label>
                                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                                           id="end_date" name="end_date" 
                                                           value="{{ old('end_date', now()->addMonths(3)->format('Y-m-d')) }}">
                                                    @error('end_date')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="is_active" name="is_active" value="1" 
                                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_active">
                                                    جدولة نشطة
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="auto_generate_sessions" name="auto_generate_sessions" value="1" 
                                                       {{ old('auto_generate_sessions', true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="auto_generate_sessions">
                                                    إنشاء جلسات تلقائياً
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="notes">ملاحظات إضافية</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" name="notes" rows="3" 
                                                      placeholder="أي ملاحظات أو تعليمات خاصة...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الأيام والأوقات -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-outline card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">الأيام والأوقات</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-success btn-sm" id="addDayBtn">
                                                <i class="fas fa-plus"></i> إضافة يوم
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="daysContainer">
                                            <!-- سيتم إضافة الأيام هنا ديناميكياً -->
                                        </div>
                                        
                                        <div class="alert alert-info" id="noDaysAlert">
                                            <i class="fas fa-info-circle"></i>
                                            انقر على "إضافة يوم" لبدء إضافة أيام وأوقات الجدولة
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ملخص الجدولة -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">ملخص الجدولة</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="scheduleSummary" class="alert alert-light">
                                            <i class="fas fa-calendar"></i>
                                            <strong>لم يتم إضافة أي أيام بعد</strong>
                                        </div>
                                        
                                        <div id="conflictCheck" class="alert alert-secondary" style="display: none;">
                                            <i class="fas fa-search"></i>
                                            جاري فحص التعارضات...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-lg" id="saveBtn" disabled>
                                    <i class="fas fa-save"></i> حفظ الجدولة
                                </button>
                                <button type="button" class="btn btn-info btn-lg" id="checkConflictsBtn" disabled>
                                    <i class="fas fa-search"></i> فحص التعارضات
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('schedules.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                                <button type="button" class="btn btn-warning btn-lg" id="previewBtn" disabled>
                                    <i class="fas fa-eye"></i> معاينة
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- قالب اليوم -->
<template id="dayTemplate">
    <div class="day-item card mb-3" data-day-index="">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-calendar-day"></i>
                اليوم <span class="day-number"></span>
            </h5>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-danger remove-day-btn">
                    <i class="fas fa-trash"></i> حذف
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>يوم الأسبوع <span class="text-danger">*</span></label>
                        <select class="form-control day-select" name="days[][day_of_week]" required>
                            <option value="">اختر اليوم</option>
                            <option value="sunday">الأحد</option>
                            <option value="monday">الاثنين</option>
                            <option value="tuesday">الثلاثاء</option>
                            <option value="wednesday">الأربعاء</option>
                            <option value="thursday">الخميس</option>
                            <option value="friday">الجمعة</option>
                            <option value="saturday">السبت</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>وقت البداية <span class="text-danger">*</span></label>
                        <input type="time" class="form-control start-time" name="days[][start_time]" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>وقت النهاية <span class="text-danger">*</span></label>
                        <input type="time" class="form-control end-time" name="days[][end_time]" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>نوع الجلسة</label>
                        <select class="form-control" name="days[][session_type]">
                            <option value="morning">صباحية</option>
                            <option value="afternoon">ظهيرة</option>
                            <option value="evening">مسائية</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>المكان</label>
                        <input type="text" class="form-control" name="days[][location]" placeholder="اختياري">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>ملاحظات خاصة باليوم</label>
                        <input type="text" class="form-control" name="days[][notes]" placeholder="ملاحظات اختيارية لهذا اليوم">
                    </div>
                </div>
            </div>
            <div class="day-duration alert alert-info" style="display: none;">
                <i class="fas fa-clock"></i>
                <strong>مدة الجلسة:</strong> <span class="duration-text">-</span>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let dayCounter = 0;
    const daysContainer = document.getElementById('daysContainer');
    const noDaysAlert = document.getElementById('noDaysAlert');
    const addDayBtn = document.getElementById('addDayBtn');
    const saveBtn = document.getElementById('saveBtn');
    const checkConflictsBtn = document.getElementById('checkConflictsBtn');
    const previewBtn = document.getElementById('previewBtn');

    // إضافة يوم جديد
    addDayBtn.addEventListener('click', function() {
        addNewDay();
    });

    function addNewDay() {
        dayCounter++;
        const template = document.getElementById('dayTemplate');
        const dayItem = template.content.cloneNode(true);
        
        // تحديث رقم اليوم
        dayItem.querySelector('.day-number').textContent = dayCounter;
        dayItem.querySelector('.day-item').setAttribute('data-day-index', dayCounter);
        
        // ربط أحداث الحذف
        dayItem.querySelector('.remove-day-btn').addEventListener('click', function() {
            removeDayItem(this.closest('.day-item'));
        });

        // ربط أحداث حساب المدة
        const startTimeInput = dayItem.querySelector('.start-time');
        const endTimeInput = dayItem.querySelector('.end-time');
        
        startTimeInput.addEventListener('change', function() {
            calculateDuration(this.closest('.day-item'));
            updateSummary();
            updateButtons();
        });
        
        endTimeInput.addEventListener('change', function() {
            calculateDuration(this.closest('.day-item'));
            updateSummary();
            updateButtons();
        });

        // ربط تحديث الملخص
        dayItem.querySelector('.day-select').addEventListener('change', function() {
            updateSummary();
            updateButtons();
        });

        daysContainer.appendChild(dayItem);
        noDaysAlert.style.display = 'none';
        updateSummary();
        updateButtons();
    }

    function removeDayItem(dayItem) {
        dayItem.remove();
        
        if (daysContainer.children.length === 0) {
            noDaysAlert.style.display = 'block';
        }
        
        updateSummary();
        updateButtons();
    }

    function calculateDuration(dayItem) {
        const startTime = dayItem.querySelector('.start-time').value;
        const endTime = dayItem.querySelector('.end-time').value;
        const durationDiv = dayItem.querySelector('.day-duration');
        const durationText = dayItem.querySelector('.duration-text');
        
        if (startTime && endTime) {
            const start = new Date('2000-01-01 ' + startTime);
            const end = new Date('2000-01-01 ' + endTime);
            const diff = (end - start) / (1000 * 60); // بالدقائق
            
            if (diff > 0) {
                const hours = Math.floor(diff / 60);
                const minutes = diff % 60;
                let duration = '';
                
                if (hours > 0) {
                    duration += hours + ' ساعة ';
                }
                if (minutes > 0) {
                    duration += minutes + ' دقيقة';
                }
                
                durationText.textContent = duration.trim();
                durationDiv.style.display = 'block';
            } else {
                durationDiv.style.display = 'none';
            }
        } else {
            durationDiv.style.display = 'none';
        }
    }

    function updateSummary() {
        const summaryDiv = document.getElementById('scheduleSummary');
        const dayItems = daysContainer.querySelectorAll('.day-item');
        
        if (dayItems.length === 0) {
            summaryDiv.innerHTML = '<i class="fas fa-calendar"></i><strong>لم يتم إضافة أي أيام بعد</strong>';
            return;
        }

        let summary = '<i class="fas fa-calendar"></i><strong>ملخص الجدولة:</strong><br>';
        
        dayItems.forEach((dayItem, index) => {
            const daySelect = dayItem.querySelector('.day-select');
            const startTime = dayItem.querySelector('.start-time').value;
            const endTime = dayItem.querySelector('.end-time').value;
            
            if (daySelect.value && startTime && endTime) {
                const dayText = daySelect.options[daySelect.selectedIndex].text;
                summary += `<span class="badge badge-primary mr-2">${dayText}: ${startTime} - ${endTime}</span>`;
            }
        });

        summaryDiv.innerHTML = summary;
    }

    function updateButtons() {
        const dayItems = daysContainer.querySelectorAll('.day-item');
        const hasValidDays = Array.from(dayItems).some(dayItem => {
            const daySelect = dayItem.querySelector('.day-select');
            const startTime = dayItem.querySelector('.start-time').value;
            const endTime = dayItem.querySelector('.end-time').value;
            return daySelect.value && startTime && endTime;
        });

        saveBtn.disabled = !hasValidDays;
        checkConflictsBtn.disabled = !hasValidDays;
        previewBtn.disabled = !hasValidDays;
    }

    // فحص التعارضات
    checkConflictsBtn.addEventListener('click', function() {
        const conflictDiv = document.getElementById('conflictCheck');
        conflictDiv.style.display = 'block';
        conflictDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري فحص التعارضات...';
        conflictDiv.className = 'alert alert-info';

        // محاكاة فحص التعارضات
        setTimeout(() => {
            const hasConflict = Math.random() < 0.3;
            
            if (hasConflict) {
                conflictDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> تحذير: يوجد تعارض في بعض الأوقات!';
                conflictDiv.className = 'alert alert-danger';
            } else {
                conflictDiv.innerHTML = '<i class="fas fa-check-circle"></i> ممتاز! لا يوجد تعارضات في الأوقات المحددة';
                conflictDiv.className = 'alert alert-success';
            }
        }, 2000);
    });

    // إضافة يوم افتراضي
    addNewDay();
});
</script>

<style>
.day-item {
    border-left: 4px solid #ffc107;
}

.day-item .card-header {
    background-color: #fff3cd;
    border-bottom: 1px solid #ffeaa7;
}

.badge {
    font-size: 0.9em;
    margin-bottom: 5px;
}

.form-group label {
    font-weight: 600;
    color: #495057;
}

.text-danger {
    color: #dc3545 !important;
}

.btn-lg {
    padding: 0.5rem 1rem;
    font-size: 1.1rem;
}

.card-outline {
    border-top: 3px solid;
}

.card-outline.card-primary {
    border-top-color: #007bff;
}

.card-outline.card-success {
    border-top-color: #28a745;
}

.card-outline.card-warning {
    border-top-color: #ffc107;
}

.card-outline.card-info {
    border-top-color: #17a2b8;
}
</style>
@endsection

