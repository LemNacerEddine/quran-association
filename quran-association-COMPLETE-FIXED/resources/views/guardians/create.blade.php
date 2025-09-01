@extends('layouts.dashboard')

@section('title', 'إضافة ولي أمر جديد')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-user-plus me-2"></i>
                        إضافة ولي أمر جديد
                    </h2>
                    <p class="text-muted mb-0">إضافة ولي أمر جديد وربطه بالطلاب</p>
                </div>
                <a href="{{ route('guardians.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة للقائمة
                </a>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('guardians.store') }}">
                        @csrf
                        
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
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" required
                                       placeholder="05xxxxxxxx">
                                <div class="form-text">سيتم إنشاء كود الدخول من آخر 4 أرقام</div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="national_id" class="form-label">رقم الهوية</label>
                                <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                                       id="national_id" name="national_id" value="{{ old('national_id') }}">
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="relationship" class="form-label">صلة القرابة <span class="text-danger">*</span></label>
                                <select class="form-select @error('relationship') is-invalid @enderror" 
                                        id="relationship" name="relationship" required>
                                    <option value="">اختر صلة القرابة</option>
                                    <option value="father" {{ old('relationship') === 'father' ? 'selected' : '' }}>الأب</option>
                                    <option value="mother" {{ old('relationship') === 'mother' ? 'selected' : '' }}>الأم</option>
                                    <option value="guardian" {{ old('relationship') === 'guardian' ? 'selected' : '' }}>ولي الأمر</option>
                                    <option value="other" {{ old('relationship') === 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('relationship')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="job" class="form-label">المهنة</label>
                                <input type="text" class="form-control @error('job') is-invalid @enderror" 
                                       id="job" name="job" value="{{ old('job') }}">
                                @error('job')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">العنوان</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="2">{{ old('address') }}</textarea>
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
                                        <br><small>يتم عرض الطلاب غير المرتبطين بأولياء أمور آخرين فقط</small>
                                    </div>
                                    
                                    <!-- حقل البحث للطلاب -->
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" id="student_search" class="form-control" 
                                                   placeholder="ابحث عن الطلاب بالاسم...">
                                        </div>
                                    </div>
                                    
                                    <div id="students-container">
                                        @foreach($students as $student)
                                        <div class="card mb-2">
                                            <div class="card-body py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-1">
                                                        <div class="form-check">
                                                            <input class="form-check-input student-checkbox" 
                                                                   type="checkbox" name="students[]" 
                                                                   value="{{ $student->id }}" 
                                                                   id="student_{{ $student->id }}"
                                                                   {{ in_array($student->id, old('students', [])) ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="student_{{ $student->id }}" class="form-check-label">
                                                            <strong>{{ $student->name }}</strong>
                                                            @if($student->circle)
                                                                <br><small class="text-muted">{{ $student->circle->name }}</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-select form-select-sm relationship-select" 
                                                                name="relationship_types[{{ $student->id }}]" disabled>
                                                            <option value="father">الأب</option>
                                                            <option value="mother">الأم</option>
                                                            <option value="guardian">ولي الأمر</option>
                                                            <option value="other">أخرى</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input primary-checkbox" 
                                                                   type="checkbox" name="is_primary[{{ $student->id }}]" 
                                                                   value="1" disabled>
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
                                        لا توجد طلاب مسجلين في النظام. يمكنك إضافة الطلاب لاحقاً.
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
                                           name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        ولي أمر نشط
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('guardians.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>
                                        إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        حفظ ولي الأمر
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
                if (mainRelationship.value) {
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
            if (!select.disabled) {
                select.value = this.value;
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
    
    // Student search functionality
    const studentSearch = document.getElementById('student_search');
    const studentsContainer = document.getElementById('students-container');
    const studentCards = studentsContainer.querySelectorAll('.card');
    
    if (studentSearch) {
        studentSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            studentCards.forEach(card => {
                const studentName = card.querySelector('label strong').textContent.toLowerCase();
                const circleName = card.querySelector('label small')?.textContent.toLowerCase() || '';
                
                if (studentName.includes(searchTerm) || circleName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show message if no results
            const visibleCards = Array.from(studentCards).filter(card => card.style.display !== 'none');
            let noResultsMsg = document.getElementById('no-results-message');
            
            if (visibleCards.length === 0 && searchTerm !== '') {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.id = 'no-results-message';
                    noResultsMsg.className = 'alert alert-warning text-center';
                    noResultsMsg.innerHTML = '<i class="fas fa-search me-2"></i>لا توجد نتائج للبحث';
                    studentsContainer.appendChild(noResultsMsg);
                }
                noResultsMsg.style.display = 'block';
            } else if (noResultsMsg) {
                noResultsMsg.style.display = 'none';
            }
        });
    }
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
}
</style>
@endsection

