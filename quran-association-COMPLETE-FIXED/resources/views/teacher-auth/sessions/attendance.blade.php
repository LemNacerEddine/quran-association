@extends('layouts.teacher')

@section('title', 'إدارة الحضور - المعلم')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>إدارة الحضور
                        </h5>
                        <a href="{{ route('teacher.sessions.show', $session) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-right me-1"></i>العودة للجلسة
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Session Info -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="alert-heading mb-2">
                                    <i class="fas fa-info-circle me-2"></i>معلومات الجلسة
                                </h6>
                                <p class="mb-1"><strong>{{ $session->title }}</strong> - {{ $session->circle->name }}</p>
                                <p class="mb-1"><strong>المعلم:</strong> {{ $teacher->name }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $session->start_time }} - {{ $session->end_time }} | {{ $session->date }}
                                </small>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="badge bg-primary fs-6 p-2">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $session->circle->students->count() }} طالب
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Points System Info -->
                    <div class="alert alert-light border mb-4">
                        <h6 class="alert-heading mb-3">
                            <i class="fas fa-star me-2 text-warning"></i>نظام النقاط المتقدم
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">نقاط الحضور:</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><i class="fas fa-check text-success me-2"></i><strong>حاضر:</strong> 5 نقاط</li>
                                    <li><i class="fas fa-clock text-warning me-2"></i><strong>متأخر (أول مرة):</strong> 3 نقاط</li>
                                    <li><i class="fas fa-clock text-orange me-2"></i><strong>متأخر (متتالي):</strong> 2 نقطة</li>
                                    <li><i class="fas fa-times text-danger me-2"></i><strong>غائب:</strong> 0 نقطة</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">نقاط الحفظ:</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2">⭐⭐⭐⭐⭐</span>
                                    <span class="text-muted">= 5 نقاط</span>
                                </div>
                                <p class="text-muted small mb-0">
                                    <strong>النقطة النهائية:</strong> نقطة الحضور + نقاط النجمات<br>
                                    <strong>ملاحظة:</strong> الغائب = 0 نقطة نهائية
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($session->circle->students->count() > 0)
                    <form method="POST" action="{{ route('teacher.sessions.attendance.store', $session) }}" id="attendanceForm">
                        @csrf
                        
                        <!-- Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body py-3">
                                        <h6 class="card-title mb-3">
                                            <i class="fas fa-bolt me-2 text-warning"></i>إجراءات سريعة
                                        </h6>
                                        <div class="btn-group flex-wrap" role="group">
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="markAll('present')">
                                                <i class="fas fa-check me-1"></i>الكل حاضر
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="markAll('late')">
                                                <i class="fas fa-clock me-1"></i>الكل متأخر
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="markAll('absent')">
                                                <i class="fas fa-times me-1"></i>الكل غائب
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAll()">
                                                <i class="fas fa-eraser me-1"></i>مسح الكل
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Students List -->
                        <div class="row">
                            @foreach($session->circle->students as $student)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="card student-card h-100" data-student-id="{{ $student->id }}">
                                    <div class="card-body">
                                        <!-- Student Info -->
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-circle bg-success text-white me-3">
                                                {{ mb_substr($student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $student->name }}</h6>
                                                <small class="text-muted">رقم الطالب: {{ $student->id }}</small>
                                            </div>
                                        </div>

                                        <!-- Attendance Radio Buttons -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-success">
                                                <i class="fas fa-clipboard-check me-1"></i>الحضور
                                            </label>
                                            <div class="attendance-options">
                                                @php
                                                    $existingAttendance = $session->attendances->where('student_id', $student->id)->first();
                                                @endphp
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input attendance-radio" type="radio" 
                                                           name="attendance[{{ $student->id }}]" 
                                                           id="present_{{ $student->id }}" 
                                                           value="present"
                                                           data-student="{{ $student->id }}"
                                                           {{ $existingAttendance && $existingAttendance->status == 'present' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-success" for="present_{{ $student->id }}">
                                                        <i class="fas fa-check me-1"></i>حاضر
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input attendance-radio" type="radio" 
                                                           name="attendance[{{ $student->id }}]" 
                                                           id="late_{{ $student->id }}" 
                                                           value="late"
                                                           data-student="{{ $student->id }}"
                                                           {{ $existingAttendance && $existingAttendance->status == 'late' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-warning" for="late_{{ $student->id }}">
                                                        <i class="fas fa-clock me-1"></i>متأخر
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input attendance-radio" type="radio" 
                                                           name="attendance[{{ $student->id }}]" 
                                                           id="absent_{{ $student->id }}" 
                                                           value="absent"
                                                           data-student="{{ $student->id }}"
                                                           {{ $existingAttendance && $existingAttendance->status == 'absent' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-danger" for="absent_{{ $student->id }}">
                                                        <i class="fas fa-times me-1"></i>غائب
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input attendance-radio" type="radio" 
                                                           name="attendance[{{ $student->id }}]" 
                                                           id="excused_{{ $student->id }}" 
                                                           value="excused"
                                                           data-student="{{ $student->id }}"
                                                           {{ $existingAttendance && $existingAttendance->status == 'excused' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-info" for="excused_{{ $student->id }}">
                                                        <i class="fas fa-info-circle me-1"></i>بعذر
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Star Rating for Memorization -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-success">
                                                <i class="fas fa-book-open me-1"></i>نقاط الحفظ
                                            </label>
                                            <div class="star-rating" data-student="{{ $student->id }}">
                                                @for($i = 1; $i <= 5; $i++)
                                                <span class="star" data-value="{{ $i }}" data-student="{{ $student->id }}">
                                                    <i class="{{ $existingAttendance && $existingAttendance->memorization_points >= $i ? 'fas fa-star text-warning' : 'far fa-star' }}"></i>
                                                </span>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="memorization_stars[{{ $student->id }}]" 
                                                   value="{{ $existingAttendance ? $existingAttendance->memorization_points : 0 }}" 
                                                   class="memorization-input" data-student="{{ $student->id }}">
                                        </div>

                                        <!-- Points Display -->
                                        <div class="points-display mb-3">
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="points-badge attendance-points" data-student="{{ $student->id }}">
                                                        <small class="text-muted d-block">حضور</small>
                                                        <span class="badge bg-secondary">0</span>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="points-badge memorization-points" data-student="{{ $student->id }}">
                                                        <small class="text-muted d-block">حفظ</small>
                                                        <span class="badge bg-warning">0</span>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="points-badge final-points" data-student="{{ $student->id }}">
                                                        <small class="text-muted d-block">المجموع</small>
                                                        <span class="badge bg-success">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Notes -->
                                        <div class="mb-0">
                                            <label class="form-label fw-bold text-success">
                                                <i class="fas fa-sticky-note me-1"></i>الملاحظات
                                            </label>
                                            <textarea class="form-control form-control-sm" 
                                                      name="notes[{{ $student->id }}]" 
                                                      rows="2" 
                                                      placeholder="ملاحظات اختيارية...">{{ $existingAttendance ? $existingAttendance->notes : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('teacher.sessions.show', $session) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>حفظ الحضور
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا يوجد طلاب في هذه الحلقة</h5>
                        <p class="text-muted">يرجى إضافة طلاب للحلقة أولاً</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize existing data on page load
    initializeExistingData();
    
    // Star rating functionality
    const stars = document.querySelectorAll('.star');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const studentId = this.dataset.student;
            const value = parseInt(this.dataset.value);
            const starContainer = this.parentElement;
            const hiddenInput = document.querySelector(`input[name="memorization_stars[${studentId}]"]`);
            
            // Update hidden input
            hiddenInput.value = value;
            
            // Update star display
            const studentStars = starContainer.querySelectorAll('.star');
            studentStars.forEach((s, index) => {
                const icon = s.querySelector('i');
                if (index < value) {
                    icon.className = 'fas fa-star text-warning';
                } else {
                    icon.className = 'far fa-star text-muted';
                }
            });
            
            // Recalculate points
            calculatePoints(studentId);
        });
        
        star.addEventListener('mouseenter', function() {
            const value = parseInt(this.dataset.value);
            const starContainer = this.parentElement;
            const studentStars = starContainer.querySelectorAll('.star');
            
            studentStars.forEach((s, index) => {
                const icon = s.querySelector('i');
                if (index < value) {
                    icon.className = 'fas fa-star text-warning';
                } else {
                    icon.className = 'far fa-star text-muted';
                }
            });
        });
        
        star.addEventListener('mouseleave', function() {
            const studentId = this.dataset.student;
            const hiddenInput = document.querySelector(`input[name="memorization_stars[${studentId}]"]`);
            const currentValue = parseInt(hiddenInput.value);
            const starContainer = this.parentElement;
            const studentStars = starContainer.querySelectorAll('.star');
            
            studentStars.forEach((s, index) => {
                const icon = s.querySelector('i');
                if (index < currentValue) {
                    icon.className = 'fas fa-star text-warning';
                } else {
                    icon.className = 'far fa-star text-muted';
                }
            });
        });
    });
    
    // Attendance radio button functionality
    const attendanceRadios = document.querySelectorAll('.attendance-radio');
    
    attendanceRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const studentId = this.dataset.student;
            calculatePoints(studentId);
        });
    });
    
    // Calculate points for a student
    function calculatePoints(studentId) {
        const attendanceRadio = document.querySelector(`input[name="attendance[${studentId}]"]:checked`);
        const memorizationInput = document.querySelector(`input[name="memorization_stars[${studentId}]"]`);
        const attendancePointsBadge = document.querySelector(`.attendance-points[data-student="${studentId}"] .badge`);
        const memorizationPointsBadge = document.querySelector(`.memorization-points[data-student="${studentId}"] .badge`);
        const finalPointsBadge = document.querySelector(`.final-points[data-student="${studentId}"] .badge`);
        
        if (!attendanceRadio || !memorizationInput) return;
        
        const status = attendanceRadio.value;
        const memorizationStars = parseInt(memorizationInput.value) || 0;
        
        // Calculate attendance points
        let attendancePoints = 0;
        let attendanceClass = 'bg-secondary';
        
        switch (status) {
            case 'present':
                attendancePoints = 5;
                attendanceClass = 'bg-success';
                break;
            case 'late':
                // TODO: Check for consecutive lateness
                attendancePoints = 3; // Default to first-time late
                attendanceClass = 'bg-warning';
                break;
            case 'absent':
            case 'excused':
                attendancePoints = 0;
                attendanceClass = 'bg-danger';
                break;
        }
        
        // Calculate final points
        let finalPoints = 0;
        if (status !== 'absent') {
            finalPoints = attendancePoints + memorizationStars;
        }
        
        // Update badges
        attendancePointsBadge.textContent = attendancePoints;
        attendancePointsBadge.className = `badge ${attendanceClass}`;
        
        memorizationPointsBadge.textContent = memorizationStars;
        
        finalPointsBadge.textContent = finalPoints;
        finalPointsBadge.className = finalPoints > 0 ? 'badge bg-success' : 'badge bg-secondary';
    }
    
    // Quick action functions
    window.markAll = function(status) {
        attendanceRadios.forEach(radio => {
            if (radio.value === status) {
                radio.checked = true;
                const studentId = radio.dataset.student;
                calculatePoints(studentId);
            }
        });
    };
    
    window.clearAll = function() {
        attendanceRadios.forEach(radio => {
            radio.checked = false;
        });
        
        // Reset all stars
        stars.forEach(star => {
            const studentId = star.dataset.student;
            const hiddenInput = document.querySelector(`input[name="memorization_stars[${studentId}]"]`);
            if (hiddenInput) {
                hiddenInput.value = 0;
            }
            star.querySelector('i').className = 'far fa-star text-muted';
        });
        
        // Reset all points
        document.querySelectorAll('.points-badge .badge').forEach(badge => {
            badge.textContent = '0';
            badge.className = 'badge bg-secondary';
        });
    };
    
    // Form validation
    const form = document.getElementById('attendanceForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedRadios = document.querySelectorAll('.attendance-radio:checked');
            
            if (checkedRadios.length === 0) {
                e.preventDefault();
                alert('يرجى تحديد حالة الحضور لطالب واحد على الأقل');
                return false;
            }
        });
    }
    
    // Initialize existing data on page load
    function initializeExistingData() {
        // Calculate points for all students with existing data
        const attendanceRadios = document.querySelectorAll('.attendance-radio:checked');
        attendanceRadios.forEach(radio => {
            const studentId = radio.dataset.student;
            calculatePoints(studentId);
        });
    }
});
</script>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

.student-card {
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.student-card:hover {
    border-color: #28a745;
    box-shadow: 0 4px 8px rgba(40,167,69,0.1);
}

.attendance-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.form-check-inline {
    margin-right: 0;
}

.star-rating {
    display: flex;
    gap: 5px;
    margin-bottom: 10px;
}

.star {
    cursor: pointer;
    font-size: 1.5rem;
    transition: all 0.2s ease;
}

.star:hover {
    transform: scale(1.1);
}

.points-display {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
}

.points-badge {
    text-align: center;
}

.points-badge .badge {
    font-size: 0.9rem;
    padding: 5px 8px;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.form-check-input:checked[value="present"] {
    background-color: #28a745;
    border-color: #28a745;
}

.form-check-input:checked[value="late"] {
    background-color: #ffc107;
    border-color: #ffc107;
}

.form-check-input:checked[value="absent"] {
    background-color: #dc3545;
    border-color: #dc3545;
}

.form-check-input:checked[value="excused"] {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

@media (max-width: 768px) {
    .attendance-options {
        flex-direction: column;
    }
    
    .form-check-inline {
        display: block;
        margin-bottom: 5px;
    }
    
    .star-rating {
        justify-content: center;
    }
}
</style>
@endsection

