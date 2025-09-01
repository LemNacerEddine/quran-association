@extends('layouts.dashboard')

@section('title', 'تسجيل الحضور للجلسة - جمعية تحفيظ القرآن الكريم')

@section('content')
<div class="container-fluid">
    {{-- عرض رسائل النجاح والأخطاء --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>يرجى تصحيح الأخطاء التالية:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary">
                    <i class="fas fa-user-check me-2"></i>
                    تسجيل الحضور للجلسة
                </h2>
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-right me-2"></i>
                    العودة لقائمة الحضور
                </a>
            </div>

            {{-- معلومات الجلسة --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات الجلسة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>الحلقة:</strong> {{ $session->circle->name }}</p>
                            <p><strong>المعلم:</strong> {{ $session->circle->teacher->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>التاريخ:</strong> {{ $session->session_date->format('Y-m-d') }}</p>
                            <p><strong>الوقت:</strong> {{ $session->circle->start_time }} - {{ $session->circle->end_time }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- نموذج تسجيل الحضور --}}
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        تسجيل حضور الطلاب
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('attendance.storeSession', $session) }}" id="attendanceForm">
                        @csrf
                        
                        {{-- أزرار الإجراءات السريعة --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success" onclick="markAll('present')">
                                        <i class="fas fa-check-circle me-1"></i>
                                        تحديد الكل حاضر
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="markAll('late')">
                                        <i class="fas fa-clock me-1"></i>
                                        تحديد الكل متأخر
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="markAll('absent')">
                                        <i class="fas fa-times-circle me-1"></i>
                                        تحديد الكل غائب
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- قائمة الطلاب --}}
                        <div class="row">
                            @foreach($session->circle->students as $student)
                                @php
                                    $attendance = $attendances->get($student->id);
                                    $currentStatus = $attendance ? $attendance->status : 'present';
                                    $currentMemorizationPoints = $attendance ? $attendance->memorization_points : 0;
                                    $currentNotes = $attendance ? $attendance->notes : '';
                                @endphp
                                
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="card student-card h-100 border-2" data-student-id="{{ $student->id }}">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-center">
                                                <i class="fas fa-user-graduate me-2"></i>
                                                {{ $student->name }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            {{-- حالة الحضور --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">حالة الحضور:</label>
                                                <div class="attendance-options">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input attendance-radio" 
                                                               type="radio" 
                                                               name="attendance[{{ $student->id }}][status]" 
                                                               id="present_{{ $student->id }}" 
                                                               value="present" 
                                                               {{ $currentStatus == 'present' ? 'checked' : '' }}
                                                               onchange="updatePoints({{ $student->id }})">
                                                        <label class="form-check-label text-success" for="present_{{ $student->id }}">
                                                            <i class="fas fa-check-circle"></i> حاضر
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input attendance-radio" 
                                                               type="radio" 
                                                               name="attendance[{{ $student->id }}][status]" 
                                                               id="late_{{ $student->id }}" 
                                                               value="late" 
                                                               {{ $currentStatus == 'late' ? 'checked' : '' }}
                                                               onchange="updatePoints({{ $student->id }})">
                                                        <label class="form-check-label text-warning" for="late_{{ $student->id }}">
                                                            <i class="fas fa-clock"></i> متأخر
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input attendance-radio" 
                                                               type="radio" 
                                                               name="attendance[{{ $student->id }}][status]" 
                                                               id="absent_{{ $student->id }}" 
                                                               value="absent" 
                                                               {{ $currentStatus == 'absent' ? 'checked' : '' }}
                                                               onchange="updatePoints({{ $student->id }})">
                                                        <label class="form-check-label text-danger" for="absent_{{ $student->id }}">
                                                            <i class="fas fa-times-circle"></i> غائب
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input attendance-radio" 
                                                               type="radio" 
                                                               name="attendance[{{ $student->id }}][status]" 
                                                               id="excused_{{ $student->id }}" 
                                                               value="excused" 
                                                               {{ $currentStatus == 'excused' ? 'checked' : '' }}
                                                               onchange="updatePoints({{ $student->id }})">
                                                        <label class="form-check-label text-info" for="excused_{{ $student->id }}">
                                                            <i class="fas fa-user-clock"></i> غياب بعذر
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- نقاط الحفظ --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">نقاط الحفظ:</label>
                                                <div class="star-rating" data-student-id="{{ $student->id }}">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="star {{ $i <= $currentMemorizationPoints ? 'active' : '' }}" 
                                                              data-rating="{{ $i }}" 
                                                              onclick="setRating({{ $student->id }}, {{ $i }})">
                                                            <i class="fas fa-star"></i>
                                                        </span>
                                                    @endfor
                                                </div>
                                                <input type="hidden" 
                                                       name="attendance[{{ $student->id }}][memorization_points]" 
                                                       id="memorization_{{ $student->id }}" 
                                                       value="{{ $currentMemorizationPoints }}">
                                            </div>

                                            {{-- الملاحظات --}}
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">الملاحظات:</label>
                                                <textarea class="form-control form-control-sm" 
                                                          name="attendance[{{ $student->id }}][notes]" 
                                                          rows="2" 
                                                          placeholder="ملاحظات اختيارية...">{{ $currentNotes }}</textarea>
                                            </div>

                                            {{-- عرض النقاط --}}
                                            <div class="points-display text-center">
                                                <div class="row">
                                                    <div class="col-4">
                                                        <small class="text-muted">حضور</small>
                                                        <div class="attendance-points fw-bold text-success" id="attendance_points_{{ $student->id }}">
                                                            5
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted">حفظ</small>
                                                        <div class="memorization-points fw-bold text-primary" id="memorization_display_{{ $student->id }}">
                                                            {{ $currentMemorizationPoints }}
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted">المجموع</small>
                                                        <div class="total-points fw-bold text-info" id="total_points_{{ $student->id }}">
                                                            {{ $currentStatus == 'absent' ? 0 : (($currentStatus == 'present' ? 5 : ($currentStatus == 'late' ? 3 : 0)) + $currentMemorizationPoints) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- أزرار الحفظ المحسنة --}}
                        <div class="mt-5">
                            <div class="row justify-content-center">
                                <div class="col-lg-10">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light text-center">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-save me-2"></i>
                                                اختر نوع الحفظ المطلوب
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                {{-- زر تسجيل الحضور فقط --}}
                                                <div class="col-md-6 mb-3">
                                                    <div class="action-card border border-warning rounded p-3 h-100">
                                                        <div class="text-center mb-3">
                                                            <i class="fas fa-user-check fa-3x text-warning mb-2"></i>
                                                            <h5 class="text-warning mb-2">تسجيل الحضور فقط</h5>
                                                            <p class="text-muted small mb-3">
                                                                سيتم حفظ حالة حضور الطلاب فقط، والجلسة ستبقى في انتظار تسجيل نقاط الحفظ لاحقاً
                                                            </p>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="submit" name="action" value="attendance_only" 
                                                                    class="btn btn-warning btn-lg px-4 py-2">
                                                                <i class="fas fa-user-check me-2"></i>
                                                                تسجيل الحضور فقط
                                                            </button>
                                                        </div>
                                                        <div class="mt-3">
                                                            <div class="alert alert-warning alert-sm mb-0">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                <strong>النتيجة:</strong> الجلسة ستنتقل إلى قائمة "في انتظار النقاط"
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- زر حفظ وتسجيل النقاط --}}
                                                <div class="col-md-6 mb-3">
                                                    <div class="action-card border border-success rounded p-3 h-100">
                                                        <div class="text-center mb-3">
                                                            <i class="fas fa-star fa-3x text-success mb-2"></i>
                                                            <h5 class="text-success mb-2">حفظ وتسجيل النقاط</h5>
                                                            <p class="text-muted small mb-3">
                                                                سيتم حفظ حالة الحضور ونقاط الحفظ معاً، والجلسة ستصبح مكتملة نهائياً
                                                            </p>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="submit" name="action" value="complete_with_points" 
                                                                    class="btn btn-success btn-lg px-4 py-2">
                                                                <i class="fas fa-star me-2"></i>
                                                                حفظ وتسجيل النقاط
                                                            </button>
                                                        </div>
                                                        <div class="mt-3">
                                                            <div class="alert alert-success alert-sm mb-0">
                                                                <i class="fas fa-check-circle me-1"></i>
                                                                <strong>النتيجة:</strong> الجلسة ستنتقل إلى قائمة "المكتملة"
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- شرح إضافي --}}
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="alert alert-info">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6 class="alert-heading">
                                                                    <i class="fas fa-lightbulb me-2"></i>
                                                                    متى تستخدم "تسجيل الحضور فقط"؟
                                                                </h6>
                                                                <ul class="mb-0 small">
                                                                    <li>عندما تريد تسجيل الحضور بسرعة</li>
                                                                    <li>عندما لم تكتمل مراجعة الحفظ بعد</li>
                                                                    <li>عندما تحتاج وقت إضافي لتقييم الطلاب</li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6 class="alert-heading">
                                                                    <i class="fas fa-trophy me-2"></i>
                                                                    متى تستخدم "حفظ وتسجيل النقاط"؟
                                                                </h6>
                                                                <ul class="mb-0 small">
                                                                    <li>عندما تكون جاهزاً لإنهاء الجلسة</li>
                                                                    <li>عندما تم تقييم جميع الطلاب</li>
                                                                    <li>عندما تريد إكمال الجلسة نهائياً</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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
    </div>
</div>

<style>
.action-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.action-card .btn {
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.action-card .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.student-card {
    transition: all 0.3s ease;
}

.student-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.attendance-options .form-check {
    margin-bottom: 8px;
}

.attendance-options .form-check-input:checked + .form-check-label {
    font-weight: bold;
}

.star-rating {
    font-size: 1.5rem;
    text-align: center;
    margin: 10px 0;
}

.star {
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s ease;
    margin: 0 2px;
}

.star:hover,
.star.active {
    color: #ffc107;
}

.star:hover ~ .star {
    color: #ddd;
}

.points-display {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px;
    margin-top: 10px;
}

.points-display .row > div {
    border-right: 1px solid #dee2e6;
}

.points-display .row > div:last-child {
    border-right: none;
}

@media (max-width: 768px) {
    .attendance-options .form-check-inline {
        display: block;
        margin-right: 0;
    }
    
    .star-rating {
        font-size: 1.2rem;
    }
}
</style>

<script>
function markAll(status) {
    const radios = document.querySelectorAll(`input[value="${status}"]`);
    radios.forEach(radio => {
        if (radio.name.includes('[status]')) {
            radio.checked = true;
            const studentId = radio.name.match(/\[(\d+)\]/)[1];
            updatePoints(studentId);
        }
    });
}

function setRating(studentId, rating) {
    // Update hidden input
    document.getElementById(`memorization_${studentId}`).value = rating;
    
    // Update star display
    const stars = document.querySelectorAll(`[data-student-id="${studentId}"] .star`);
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
    
    // Update points display
    document.getElementById(`memorization_display_${studentId}`).textContent = rating;
    updatePoints(studentId);
}

function updatePoints(studentId) {
    const statusRadio = document.querySelector(`input[name="attendance[${studentId}][status]"]:checked`);
    const memorizationPoints = parseInt(document.getElementById(`memorization_${studentId}`).value) || 0;
    
    let attendancePoints = 0;
    let totalPoints = 0;
    
    if (statusRadio) {
        const status = statusRadio.value;
        
        switch(status) {
            case 'present':
                attendancePoints = 5;
                break;
            case 'late':
                attendancePoints = 3; // TODO: Check for consecutive lateness
                break;
            case 'absent':
            case 'excused':
                attendancePoints = 0;
                break;
        }
        
        // If absent, total points = 0 regardless of memorization points
        if (status === 'absent') {
            totalPoints = 0;
        } else {
            totalPoints = attendancePoints + memorizationPoints;
        }
    }
    
    // Update display
    document.getElementById(`attendance_points_${studentId}`).textContent = attendancePoints;
    document.getElementById(`total_points_${studentId}`).textContent = totalPoints;
    
    // Update card border color based on status
    const card = document.querySelector(`[data-student-id="${studentId}"]`);
    card.classList.remove('border-success', 'border-warning', 'border-danger', 'border-info');
    
    if (statusRadio) {
        switch(statusRadio.value) {
            case 'present':
                card.classList.add('border-success');
                break;
            case 'late':
                card.classList.add('border-warning');
                break;
            case 'absent':
                card.classList.add('border-danger');
                break;
            case 'excused':
                card.classList.add('border-info');
                break;
        }
    }
}

// Initialize points on page load
document.addEventListener('DOMContentLoaded', function() {
    @foreach($session->circle->students as $student)
        updatePoints({{ $student->id }});
    @endforeach
    
    // إذا تم تسجيل الحضور بنجاح، أعد التوجيه بعد 3 ثوانٍ
    @if(session('success'))
        setTimeout(function() {
            window.location.href = "{{ route('attendance.index') }}";
        }, 3000);
        
        // إضافة رسالة تأكيد
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            const countdown = document.createElement('div');
            countdown.className = 'mt-2';
            countdown.innerHTML = '<small><i class="fas fa-clock me-1"></i>سيتم إعادة التوجيه خلال <span id="countdown">3</span> ثوانٍ...</small>';
            successAlert.appendChild(countdown);
            
            let timeLeft = 3;
            const timer = setInterval(function() {
                timeLeft--;
                document.getElementById('countdown').textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                }
            }, 1000);
        }
    @endif
});
</script>
@endsection

