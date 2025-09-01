@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        تعديل الحلقة: {{ $circle->name }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('circles.update', $circle->id) }}" method="POST" id="circleForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- اسم الحلقة -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">
                                    <i class="fas fa-tag text-primary me-1"></i>
                                    اسم الحلقة
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $circle->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- المعلم -->
                            <div class="col-md-6 mb-3">
                                <label for="teacher_id" class="form-label fw-bold">
                                    <i class="fas fa-chalkboard-teacher text-success me-1"></i>
                                    المعلم
                                </label>
                                <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                        id="teacher_id" name="teacher_id" required>
                                    <option value="">اختر المعلم</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" 
                                                {{ old('teacher_id', $circle->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- الوصف -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">
                                <i class="fas fa-align-left text-info me-1"></i>
                                الوصف
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $circle->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- المستوى -->
                            <div class="col-md-4 mb-3">
                                <label for="level" class="form-label fw-bold">
                                    <i class="fas fa-layer-group text-warning me-1"></i>
                                    المستوى
                                </label>
                                <select class="form-select @error('level') is-invalid @enderror" 
                                        id="level" name="level" required>
                                    <option value="مبتدئ" {{ old('level', $circle->level) == 'مبتدئ' ? 'selected' : '' }}>مبتدئ</option>
                                    <option value="متوسط" {{ old('level', $circle->level) == 'متوسط' ? 'selected' : '' }}>متوسط</option>
                                    <option value="متقدم" {{ old('level', $circle->level) == 'متقدم' ? 'selected' : '' }}>متقدم</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- وقت البداية -->
                            <div class="col-md-4 mb-3">
                                <label for="start_time" class="form-label fw-bold">
                                    <i class="fas fa-clock text-success me-1"></i>
                                    وقت البداية
                                </label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" 
                                       value="{{ old('start_time', $circle->start_time ? \Carbon\Carbon::parse($circle->start_time)->format('H:i') : '06:00') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- وقت النهاية -->
                            <div class="col-md-4 mb-3">
                                <label for="end_time" class="form-label fw-bold">
                                    <i class="fas fa-clock text-danger me-1"></i>
                                    وقت النهاية
                                </label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" 
                                       value="{{ old('end_time', $circle->end_time ? \Carbon\Carbon::parse($circle->end_time)->format('H:i') : '07:00') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- أيام الحلقة - التصميم الجديد المحسن -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">
                                <i class="fas fa-calendar-week text-primary me-1"></i>
                                أيام الحلقة
                            </label>
                            
                            @php
                                // تحويل الأيام العربية إلى إنجليزية للمقارنة
                                $arabicToEnglish = [
                                    'الأحد' => 'sunday',
                                    'الاثنين' => 'monday', 
                                    'الثلاثاء' => 'tuesday',
                                    'الأربعاء' => 'wednesday',
                                    'الخميس' => 'thursday',
                                    'الجمعة' => 'friday',
                                    'السبت' => 'saturday'
                                ];

                                $circleDays = $circle->days ? explode(',', $circle->days) : [];
                                $selectedDays = [];
                                foreach($circleDays as $day) {
                                    $day = trim($day);
                                    if (isset($arabicToEnglish[$day])) {
                                        $selectedDays[] = $arabicToEnglish[$day];
                                    } else {
                                        $selectedDays[] = $day; // إذا كان إنجليزي أصلاً
                                    }
                                }
                                $selectedDays = old("days", $selectedDays);

                                $daysOfWeek = [
                                    'sunday' => ['name' => 'الأحد', 'color' => 'danger', 'icon' => 'fas fa-sun'],
                                    'monday' => ['name' => 'الاثنين', 'color' => 'primary', 'icon' => 'fas fa-moon'],
                                    'tuesday' => ['name' => 'الثلاثاء', 'color' => 'success', 'icon' => 'fas fa-star'],
                                    'wednesday' => ['name' => 'الأربعاء', 'color' => 'warning', 'icon' => 'fas fa-heart'],
                                    'thursday' => ['name' => 'الخميس', 'color' => 'info', 'icon' => 'fas fa-gem'],
                                    'friday' => ['name' => 'الجمعة', 'color' => 'success', 'icon' => 'fas fa-mosque'],
                                    'saturday' => ['name' => 'السبت', 'color' => 'secondary', 'icon' => 'fas fa-calendar']
                                ];
                            @endphp

                            <div class="row g-2">
                                @foreach($daysOfWeek as $key => $day)
                                    <div class="col-md-3 col-sm-4 col-6">
                                        <div class="day-card position-relative">
                                            <input type="checkbox" 
                                                   class="btn-check" 
                                                   id="day_{{ $key }}" 
                                                   name="schedule_days[]" 
                                                   value="{{ $key }}"
                                                   {{ in_array($key, $selectedDays) ? "checked" : "" }}>
                                            <label class="btn btn-outline-{{ $day['color'] }} w-100 day-label" 
                                                   for="day_{{ $key }}">
                                                <div class="d-flex flex-column align-items-center py-2">
                                                    <i class="{{ $day['icon'] }} fa-lg mb-1"></i>
                                                    <span class="fw-bold small">{{ $day['name'] }}</span>
                                                </div>
                                                <div class="selected-indicator">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @error('schedule_days')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- الموقع -->
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label fw-bold">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    الموقع
                                </label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $circle->location) }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الحد الأقصى للطلاب -->
                            <div class="col-md-6 mb-3">
                                <label for="max_students" class="form-label fw-bold">
                                    <i class="fas fa-users text-info me-1"></i>
                                    الحد الأقصى للطلاب
                                </label>
                                <input type="number" class="form-control @error('max_students') is-invalid @enderror" 
                                       id="max_students" name="max_students" min="1" 
                                       value="{{ old('max_students', $circle->max_students) }}">
                                @error('max_students')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                                               <!-- حالة النشاط -->
                            <div class="col-12 mb-4">
                                <div class="d-flex align-items-center">
                                    <input class="form-check-input m-0 me-2" type="checkbox" id="is_active" name="is_active" 
                                           {{ old('is_active', $circle->is_active) ? 'checked' : '' }}>
                                    <label class="fw-bold m-0" for="is_active">نشط</label>
                                </div>
                            </div>
                        </div>

                        <!-- قسم تحديد الطلاب -->
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-users me-2"></i>تحديد الطلاب في الحلقة
                                    </h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-success" id="selectAllStudents">
                                            <i class="fas fa-check-double me-1"></i>تحديد الكل
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" id="deselectAllStudents">
                                            <i class="fas fa-times me-1"></i>إلغاء تحديد الكل
                                        </button>
                                        <button type="button" class="btn btn-sm btn-info" id="toggleSelection">
                                            <i class="fas fa-exchange-alt me-1"></i>عكس التحديد
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">اختر الطلاب:</label>
                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>عدد الطلاب المحددين حالياً: </strong>
                                            <span id="selectedCount" class="badge bg-primary">{{ $circle->students ? $circle->students->count() : 0 }}</span>
                                            من أصل <span class="badge bg-secondary">{{ $students ? $students->count() : 0 }}</span>
                                        </div>
                                        <div class="form-check-container" style="max-height: 350px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 5px; background-color: #f8f9fa;">
                                            @if($students && $students->count() > 0)
                                                @foreach($students as $student)
                                                    <div class="form-check mb-3 p-2 border rounded" style="background-color: white;">
                                                        <input class="form-check-input student-checkbox" type="checkbox" name="students[]" value="{{ $student->id }}" id="student_{{ $student->id }}"
                                                               @if($circle->students && $circle->students->contains($student->id)) checked @endif>
                                                        <label class="form-check-label w-100" for="student_{{ $student->id }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <strong class="text-dark">{{ $student->name }}</strong>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-phone me-1"></i>{{ $student->phone ?? 'لا يوجد رقم' }}
                                                                        @if($student->age)
                                                                            | <i class="fas fa-birthday-cake me-1"></i>{{ $student->age }} سنة
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                                <div>
                                                                    @if($student->gender)
                                                                        <span class="badge bg-{{ $student->gender == 'male' ? 'primary' : 'pink' }} me-2">
                                                                            {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}
                                                                        </span>
                                                                    @endif
                                                                    @if($circle->students && $circle->students->contains($student->id))
                                                                        <span class="badge bg-success">مسجل حالياً</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">غير مسجل</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted">لا توجد طلاب متاحون للتسجيل</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-3">
                                            <small class="form-text text-muted">
                                                <i class="fas fa-lightbulb me-1 text-warning"></i>
                                                <strong>نصائح:</strong>
                                                <ul class="mb-0 mt-1">
                                                    <li>يمكنك اختيار عدة طلاب للحلقة الواحدة</li>
                                                    <li>يمكن للطالب الواحد التسجيل في حلقات متعددة</li>
                                                    <li>إلغاء تحديد طالب سيؤدي إلى إزالته من الحلقة</li>
                                                    <li>استخدم الأزرار أعلاه للتحديد السريع</li>
                                                </ul>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الأزرار -->
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <a href="{{ route('circles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.day-card {
    transition: all 0.3s ease;
}

.day-label {
    border: 2px solid;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    min-height: 80px;
    font-size: 0.9rem;
}

.day-label:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.btn-check:checked + .day-label {
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-width: 3px;
}

.selected-indicator {
    position: absolute;
    top: 8px;
    right: 8px;
    color: white;
    background: rgba(0,0,0,0.7);
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.btn-check:checked + .day-label .selected-indicator {
    opacity: 1;
    transform: scale(1.1);
}

.btn-check:checked + .btn-outline-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-check:checked + .btn-outline-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.btn-check:checked + .btn-outline-success {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.btn-check:checked + .btn-outline-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: black;
}

.btn-check:checked + .btn-outline-info {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: black;
}

.btn-check:checked + .btn-outline-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

/* تأثيرات إضافية */
.card {
    border: none;
    border-radius: 20px;
}

.card-header {
    border-radius: 20px 20px 0 0 !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديث عداد الأيام المحددة
    function updateSelectedCount() {
        const selectedDays = document.querySelectorAll('input[name="schedule_days[]"]:checked');
        const count = selectedDays.length;
        console.log(`تم تحديد ${count} أيام`);
    }
    
    // مراقبة تغيير الأيام
    document.querySelectorAll('input[name="schedule_days[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // تحديث العداد عند التحميل
    updateSelectedCount();
    
    // معالج إرسال النموذج مع إصلاح المشكلة
    const form = document.getElementById('circleForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('تم إرسال النموذج - جاري الحفظ...');
            
            // إظهار رسالة تحميل جميلة
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> جاري الحفظ...';
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-secondary');
            submitBtn.classList.remove('btn-primary');
            
            // إرسال البيانات مع إصلاح المشكلة
            const formData = new FormData(form);
            
            // إصلاح مشكلة checkbox is_active
            const isActiveCheckbox = form.querySelector('input[name="is_active"]');
            if (isActiveCheckbox) {
                formData.delete('is_active');
                formData.append('is_active', isActiveCheckbox.checked ? '1' : '0');
            }
            
            // الحل: استخدام XMLHttpRequest بدلاً من fetch
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                // رسالة نجاح جميلة
                                submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> تم الحفظ بنجاح!';
                                submitBtn.classList.remove('btn-secondary');
                                submitBtn.classList.add('btn-success');
                                
                                // التوجيه بعد ثانيتين
                                setTimeout(() => {
                                    window.location.href = '/circles';
                                }, 1500);
                            } else {
                                throw new Error(response.message || 'فشل في الحفظ');
                            }
                        } catch (e) {
                            console.error('خطأ في تحليل الاستجابة:', e);
                            showError('حدث خطأ في معالجة الاستجابة');
                        }
                    } else {
                        console.error('خطأ HTTP:', xhr.status);
                        showError('حدث خطأ في الاتصال بالخادم (كود: ' + xhr.status + ')');
                    }
                }
            };
            
            xhr.onerror = function() {
                console.error('خطأ في الشبكة');
                showError('حدث خطأ في الاتصال بالشبكة');
            };
            
            xhr.send(formData);
            
            function showError(message) {
                alert(message + '. يرجى المحاولة مرة أخرى.');
                
                // إعادة تفعيل الزر
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary', 'btn-success');
                submitBtn.classList.add('btn-primary');
            }
        });
    }
    
    // وظائف التحديد المتعدد للطلاب
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        const selectedCountElement = document.getElementById('selectedCount');
        if (selectedCountElement) {
            selectedCountElement.textContent = checkedBoxes.length;
            selectedCountElement.className = checkedBoxes.length > 0 ? 'badge bg-primary' : 'badge bg-secondary';
        }
    }
    
    // تحديد جميع الطلاب
    document.getElementById('selectAllStudents')?.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
            // تحديث مظهر البطاقة
            const card = checkbox.closest('.form-check');
            if (card) {
                card.style.backgroundColor = '#e8f5e8';
                card.style.borderColor = '#28a745';
            }
        });
        updateSelectedCount();
        
        // تأثير بصري للزر
        this.innerHTML = '<i class="fas fa-check me-1"></i>تم التحديد!';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-check-double me-1"></i>تحديد الكل';
        }, 1000);
    });
    
    // إلغاء تحديد جميع الطلاب
    document.getElementById('deselectAllStudents')?.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            // تحديث مظهر البطاقة
            const card = checkbox.closest('.form-check');
            if (card) {
                card.style.backgroundColor = 'white';
                card.style.borderColor = '#ddd';
            }
        });
        updateSelectedCount();
        
        // تأثير بصري للزر
        this.innerHTML = '<i class="fas fa-check me-1"></i>تم الإلغاء!';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-times me-1"></i>إلغاء تحديد الكل';
        }, 1000);
    });
    
    // عكس التحديد
    document.getElementById('toggleSelection')?.addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = !checkbox.checked;
            // تحديث مظهر البطاقة
            const card = checkbox.closest('.form-check');
            if (card) {
                if (checkbox.checked) {
                    card.style.backgroundColor = '#e8f5e8';
                    card.style.borderColor = '#28a745';
                } else {
                    card.style.backgroundColor = 'white';
                    card.style.borderColor = '#ddd';
                }
            }
        });
        updateSelectedCount();
        
        // تأثير بصري للزر
        this.innerHTML = '<i class="fas fa-sync fa-spin me-1"></i>جاري العكس...';
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-exchange-alt me-1"></i>عكس التحديد';
        }, 800);
    });
    
    // مراقبة تغيير حالة الـ checkboxes
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            
            // تحديث مظهر البطاقة
            const card = this.closest('.form-check');
            if (card) {
                if (this.checked) {
                    card.style.backgroundColor = '#e8f5e8';
                    card.style.borderColor = '#28a745';
                    card.style.transition = 'all 0.3s ease';
                } else {
                    card.style.backgroundColor = 'white';
                    card.style.borderColor = '#ddd';
                    card.style.transition = 'all 0.3s ease';
                }
            }
        });
        
        // تطبيق المظهر الأولي
        const card = checkbox.closest('.form-check');
        if (card && checkbox.checked) {
            card.style.backgroundColor = '#e8f5e8';
            card.style.borderColor = '#28a745';
        }
    });
    
    // تحديث العداد عند تحميل الصفحة
    updateSelectedCount();
});
</script>
@endsection

