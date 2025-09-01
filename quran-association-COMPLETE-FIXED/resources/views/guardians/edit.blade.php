@extends('layouts.dashboard')

@section('title', 'تعديل ولي الأمر - ' . $guardian->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-user-edit me-2"></i>
                        تعديل ولي الأمر
                    </h2>
                    <p class="text-muted mb-0">تعديل بيانات {{ $guardian->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('guardians.show', $guardian) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>
                        عرض التفاصيل
                    </a>
                    <a href="{{ route('guardians.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('guardians.update', $guardian) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    المعلومات الأساسية
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">اسم ولي الأمر <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $guardian->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $guardian->phone) }}" required
                                       placeholder="05xxxxxxxx">
                                <div class="form-text">
                                    الكود الحالي: <code>{{ $guardian->access_code }}</code>
                                    - سيتم تحديثه عند تغيير الرقم
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $guardian->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="national_id" class="form-label">رقم الهوية</label>
                                <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                                       id="national_id" name="national_id" value="{{ old('national_id', $guardian->national_id) }}">
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="relationship" class="form-label">صلة القرابة <span class="text-danger">*</span></label>
                                <select class="form-select @error('relationship') is-invalid @enderror" 
                                        id="relationship" name="relationship" required>
                                    <option value="">اختر صلة القرابة</option>
                                    <option value="father" {{ old('relationship', $guardian->relationship) === 'father' ? 'selected' : '' }}>الأب</option>
                                    <option value="mother" {{ old('relationship', $guardian->relationship) === 'mother' ? 'selected' : '' }}>الأم</option>
                                    <option value="guardian" {{ old('relationship', $guardian->relationship) === 'guardian' ? 'selected' : '' }}>ولي الأمر</option>
                                    <option value="other" {{ old('relationship', $guardian->relationship) === 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('relationship')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="job" class="form-label">المهنة</label>
                                <input type="text" class="form-control @error('job') is-invalid @enderror" 
                                       id="job" name="job" value="{{ old('job', $guardian->job) }}">
                                @error('job')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">العنوان</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="2">{{ old('address', $guardian->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Students Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    ربط الطلاب
                                </h5>
                            </div>
                            
                            @if($students->count() > 0)
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        اختر الطلاب الذين يتكفل بهم ولي الأمر وحدد نوع العلاقة لكل طالب
                                    </div>
                                    
                                    <div id="students-container">
                                        @foreach($students as $student)
                                        @php
                                            $isSelected = $guardian->students->contains($student->id);
                                            $pivotData = $isSelected ? $guardian->students->find($student->id)->pivot : null;
                                        @endphp
                                        <div class="card mb-2">
                                            <div class="card-body py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input student-checkbox" 
                                                                   type="checkbox" name="students[]" 
                                                                   value="{{ $student->id }}" 
                                                                   id="student_{{ $student->id }}"
                                                                   {{ $isSelected || in_array($student->id, old('students', [])) ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="student_{{ $student->id }}" class="form-check-label">
                                                            <strong>{{ $student->name }}</strong>
                                                            @if($student->circle)
                                                                <br><small class="text-muted">{{ $student->circle->name }}</small>
                                                            @else
                                                                <br><small class="text-muted">غير مسجل في حلقة</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm relationship-select" 
                                                                name="relationship_types[]" {{ !$isSelected ? 'disabled' : '' }}>
                                                            <option value="father" {{ ($pivotData && $pivotData->relationship_type === 'father') ? 'selected' : '' }}>الأب</option>
                                                            <option value="mother" {{ ($pivotData && $pivotData->relationship_type === 'mother') ? 'selected' : '' }}>الأم</option>
                                                            <option value="guardian" {{ ($pivotData && $pivotData->relationship_type === 'guardian') ? 'selected' : '' }}>ولي الأمر</option>
                                                            <option value="other" {{ ($pivotData && $pivotData->relationship_type === 'other') ? 'selected' : '' }}>أخرى</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input primary-checkbox" 
                                                                   type="checkbox" name="is_primary[]" 
                                                                   value="{{ $student->id }}" 
                                                                   {{ ($pivotData && $pivotData->is_primary) ? 'checked' : '' }}
                                                                   {{ !$isSelected ? 'disabled' : '' }}>
                                                            <label class="form-check-label">
                                                                <small>أساسي</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        لا توجد طلاب مسجلين في النظام.
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Additional Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-cog me-2"></i>
                                    إعدادات إضافية
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" 
                                           name="is_active" value="1" 
                                           {{ old('is_active', $guardian->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        ولي أمر نشط
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3">{{ old('notes', $guardian->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Access Code Info -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-key me-2"></i>
                                        معلومات الدخول الحالية
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>رقم الهاتف:</strong> {{ $guardian->phone }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>كود الدخول:</strong> <code>{{ $guardian->access_code }}</code>
                                        </div>
                                    </div>
                                    <hr>
                                    <small class="mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        سيتم تحديث كود الدخول تلقائياً إذا تم تغيير رقم الهاتف
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('guardians.show', $guardian) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>
                                        إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        حفظ التعديلات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle student selection
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const relationshipSelects = document.querySelectorAll('.relationship-select');
    const primaryCheckboxes = document.querySelectorAll('.primary-checkbox');
    const mainRelationship = document.getElementById('relationship');
    
    // Enable/disable relationship selects based on student selection
    studentCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            const relationshipSelect = relationshipSelects[index];
            const primaryCheckbox = primaryCheckboxes[index];
            
            if (this.checked) {
                relationshipSelect.disabled = false;
                primaryCheckbox.disabled = false;
                // Set default relationship type from main relationship
                if (mainRelationship.value && relationshipSelect.value === '') {
                    relationshipSelect.value = mainRelationship.value;
                }
            } else {
                relationshipSelect.disabled = true;
                primaryCheckbox.disabled = true;
                primaryCheckbox.checked = false;
            }
        });
    });
    
    // Update relationship types when main relationship changes
    mainRelationship.addEventListener('change', function() {
        relationshipSelects.forEach((select, index) => {
            if (!select.disabled && studentCheckboxes[index].checked) {
                // Only update if not already set
                if (select.value === '' || confirm('هل تريد تحديث نوع العلاقة لجميع الطلاب المحددين؟')) {
                    select.value = this.value;
                }
            }
        });
    });
    
    // Ensure only one primary guardian per student
    primaryCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                primaryCheckboxes.forEach((otherCheckbox) => {
                    if (otherCheckbox !== this && otherCheckbox.value === this.value) {
                        otherCheckbox.checked = false;
                    }
                });
            }
        });
    });
    
    // Phone number change warning
    const phoneInput = document.getElementById('phone');
    const originalPhone = '{{ $guardian->phone }}';
    
    phoneInput.addEventListener('change', function() {
        if (this.value !== originalPhone) {
            const newCode = this.value.slice(-4);
            if (confirm(`سيتم تحديث كود الدخول إلى: ${newCode}\nهل تريد المتابعة؟`)) {
                // Show new code preview
                const formText = this.nextElementSibling;
                formText.innerHTML = `الكود الجديد سيكون: <code>${newCode}</code>`;
                formText.className = 'form-text text-warning';
            } else {
                this.value = originalPhone;
            }
        }
    });
});
</script>

<style>
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .row.align-items-center > div {
        margin-bottom: 0.5rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 > * {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection

