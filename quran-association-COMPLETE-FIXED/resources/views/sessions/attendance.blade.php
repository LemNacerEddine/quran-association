@extends('layouts.dashboard')

@section('title', 'تسجيل الحضور - ' . $session->session_title)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">تسجيل الحضور</h1>
            <p class="text-muted">{{ $session->session_title }} - {{ $session->session_date->format('Y-m-d') }}</p>
        </div>
        <div>
            <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> العودة للجلسات
            </a>
            <button type="button" class="btn btn-success" onclick="saveAttendance()">
                <i class="fas fa-save"></i> حفظ الحضور
            </button>
        </div>
    </div>

    <!-- Session Info -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الجلسة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>الحلقة:</strong> {{ $session->circle->name }}</p>
                            <p><strong>المعلم:</strong> {{ $session->teacher->name }}</p>
                            <p><strong>التاريخ:</strong> {{ $session->session_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>الوقت المجدول:</strong> {{ $session->schedule ? $session->schedule->formatted_time : 'غير محدد' }}</p>
                            <p><strong>الحالة:</strong> 
                                <span class="badge bg-{{ $session->status === 'ongoing' ? 'warning' : ($session->status === 'completed' ? 'success' : 'secondary') }}">
                                    {{ $session->status_name }}
                                </span>
                            </p>
                            <p><strong>إجمالي الطلاب:</strong> {{ $session->circle->students->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">إحصائيات الحضور</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="attendance-stats">
                            <div class="stat-circle present" data-percentage="{{ $session->total_students > 0 ? round(($session->present_students / $session->total_students) * 100) : 0 }}">
                                <span class="stat-number">{{ $session->present_students }}</span>
                                <span class="stat-label">حاضر</span>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number text-danger">{{ $session->absent_students }}</div>
                                    <div class="stat-label">غائب</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number text-info">{{ number_format($session->attendance_percentage, 1) }}%</div>
                                    <div class="stat-label">نسبة الحضور</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">إجراءات سريعة</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-success me-2" onclick="markAllPresent()">
                        <i class="fas fa-user-check"></i> تحديد الكل حاضر
                    </button>
                    <button type="button" class="btn btn-warning me-2" onclick="markAllAbsent()">
                        <i class="fas fa-user-times"></i> تحديد الكل غائب
                    </button>
                </div>
                <div class="col-md-6 text-end">
                    <div class="input-group" style="max-width: 300px; margin-left: auto;">
                        <input type="text" class="form-control" placeholder="البحث عن طالب..." id="studentSearch" onkeyup="searchStudents()">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Form -->
    <form id="attendanceForm" action="{{ route('sessions.update-attendance', $session) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">قائمة الطلاب</h6>
            </div>
            <div class="card-body">
                <div class="row" id="studentsContainer">
                    @foreach($session->circle->students as $index => $student)
                        @php
                            $attendance = $session->attendanceSessions->where('student_id', $student->id)->first();
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-4 student-card" data-student-name="{{ strtolower($student->name) }}">
                            <div class="card student-attendance-card {{ $attendance ? 'status-' . $attendance->status : 'status-present' }}">
                                <div class="card-body">
                                    <!-- Student Info -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="student-avatar">
                                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0">{{ $student->name }}</h6>
                                            <small class="text-muted">{{ $student->age }} سنة</small>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="status-indicator {{ $attendance ? $attendance->status : 'present' }}"></div>
                                        </div>
                                    </div>

                                    <!-- Attendance Status -->
                                    <div class="mb-3">
                                        <label class="form-label small">حالة الحضور</label>
                                        <select name="attendances[{{ $index }}][status]" class="form-select form-select-sm attendance-status" onchange="updateStudentCard(this)">
                                            <option value="present" {{ ($attendance && $attendance->status === 'present') || !$attendance ? 'selected' : '' }}>حاضر</option>
                                            <option value="absent" {{ $attendance && $attendance->status === 'absent' ? 'selected' : '' }}>غائب</option>
                                            <option value="late" {{ $attendance && $attendance->status === 'late' ? 'selected' : '' }}>متأخر</option>
                                            <option value="excused" {{ $attendance && $attendance->status === 'excused' ? 'selected' : '' }}>غياب بعذر</option>
                                        </select>
                                        <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    </div>

                                    <!-- Arrival Time (for present/late) -->
                                    <div class="mb-2 arrival-time-section" style="{{ ($attendance && in_array($attendance->status, ['present', 'late'])) || !$attendance ? '' : 'display: none;' }}">
                                        <label class="form-label small">وقت الوصول</label>
                                        <input type="time" name="attendances[{{ $index }}][arrival_time]" class="form-control form-control-sm" 
                                               value="{{ $attendance && $attendance->arrival_time ? $attendance->arrival_time->format('H:i') : '' }}">
                                    </div>

                                    <!-- Absence Reason (for absent/excused) -->
                                    <div class="mb-2 absence-reason-section" style="{{ $attendance && in_array($attendance->status, ['absent', 'excused']) ? '' : 'display: none;' }}">
                                        <label class="form-label small">سبب الغياب</label>
                                        <select name="attendances[{{ $index }}][absence_reason_id]" class="form-select form-select-sm">
                                            <option value="">اختر السبب</option>
                                            @foreach($absenceReasons as $reason)
                                                <option value="{{ $reason->id }}" {{ $attendance && $attendance->absence_reason_id == $reason->id ? 'selected' : '' }}>
                                                    {{ $reason->reason_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <textarea name="attendances[{{ $index }}][absence_reason]" class="form-control form-control-sm mt-1" 
                                                  rows="2" placeholder="تفاصيل إضافية...">{{ $attendance ? $attendance->absence_reason : '' }}</textarea>
                                    </div>

                                    <!-- Participation Score -->
                                    <div class="mb-2">
                                        <label class="form-label small">درجة المشاركة (من 10)</label>
                                        <input type="number" name="attendances[{{ $index }}][participation_score]" 
                                               class="form-control form-control-sm" min="0" max="10" step="0.5"
                                               value="{{ $attendance ? $attendance->participation_score : '' }}">
                                    </div>

                                    <!-- Notes -->
                                    <div class="mb-2">
                                        <label class="form-label small">ملاحظات</label>
                                        <textarea name="attendances[{{ $index }}][notes]" class="form-control form-control-sm" 
                                                  rows="2" placeholder="ملاحظات إضافية...">{{ $attendance ? $attendance->notes : '' }}</textarea>
                                    </div>

                                    <!-- Behavior Notes -->
                                    <div>
                                        <label class="form-label small">ملاحظات السلوك</label>
                                        <textarea name="attendances[{{ $index }}][behavior_notes]" class="form-control form-control-sm" 
                                                  rows="2" placeholder="ملاحظات السلوك...">{{ $attendance ? $attendance->behavior_notes : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($session->circle->students->count() === 0)
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-600">لا يوجد طلاب في هذه الحلقة</h5>
                        <p class="text-muted">يرجى إضافة طلاب للحلقة أولاً.</p>
                    </div>
                @endif
            </div>
            
            @if($session->circle->students->count() > 0)
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                آخر تحديث: {{ $session->attendance_taken_at ? $session->attendance_taken_at->format('Y-m-d H:i') : 'لم يتم التحديث بعد' }}
                            </small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                <i class="fas fa-undo"></i> إعادة تعيين
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> حفظ الحضور
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.student-attendance-card {
    transition: all 0.3s ease;
    border: 2px solid #e3e6f0;
}

.student-attendance-card.status-present {
    border-color: #1cc88a;
    background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
}

.student-attendance-card.status-absent {
    border-color: #e74a3b;
    background: linear-gradient(135deg, #fff8f8 0%, #f8e8e8 100%);
}

.student-attendance-card.status-late {
    border-color: #f6c23e;
    background: linear-gradient(135deg, #fffdf8 0%, #f8f4e8 100%);
}

.student-attendance-card.status-excused {
    border-color: #36b9cc;
    background: linear-gradient(135deg, #f8fdff 0%, #e8f4f8 100%);
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.status-indicator.present {
    background-color: #1cc88a;
}

.status-indicator.absent {
    background-color: #e74a3b;
}

.status-indicator.late {
    background-color: #f6c23e;
}

.status-indicator.excused {
    background-color: #36b9cc;
}

.attendance-stats {
    position: relative;
    display: inline-block;
}

.stat-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: conic-gradient(#1cc88a 0deg, #1cc88a calc(var(--percentage) * 3.6deg), #e3e6f0 calc(var(--percentage) * 3.6deg), #e3e6f0 360deg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 auto;
}

.stat-circle::before {
    content: '';
    position: absolute;
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
}

.stat-number {
    font-size: 1.2rem;
    font-weight: bold;
    color: #1cc88a;
    z-index: 1;
}

.stat-label {
    font-size: 0.7rem;
    color: #6c757d;
    z-index: 1;
}

.stat-item {
    text-align: center;
}

.stat-item .stat-number {
    font-size: 1.1rem;
    font-weight: bold;
    display: block;
}

.stat-item .stat-label {
    font-size: 0.8rem;
    color: #6c757d;
}

.student-avatar {
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .student-card {
        margin-bottom: 1rem;
    }
    
    .stat-circle {
        width: 60px;
        height: 60px;
    }
    
    .stat-circle::before {
        width: 45px;
        height: 45px;
    }
    
    .stat-number {
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
function updateStudentCard(selectElement) {
    const card = selectElement.closest('.student-attendance-card');
    const status = selectElement.value;
    const arrivalTimeSection = card.querySelector('.arrival-time-section');
    const absenceReasonSection = card.querySelector('.absence-reason-section');
    const statusIndicator = card.querySelector('.status-indicator');
    
    // Update card class
    card.className = card.className.replace(/status-\w+/, 'status-' + status);
    
    // Update status indicator
    statusIndicator.className = statusIndicator.className.replace(/\b(present|absent|late|excused)\b/, status);
    
    // Show/hide sections based on status
    if (status === 'present' || status === 'late') {
        arrivalTimeSection.style.display = 'block';
        absenceReasonSection.style.display = 'none';
        
        // Set current time for arrival if not set
        const arrivalTimeInput = arrivalTimeSection.querySelector('input[type="time"]');
        if (!arrivalTimeInput.value) {
            const now = new Date();
            const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                             now.getMinutes().toString().padStart(2, '0');
            arrivalTimeInput.value = timeString;
        }
    } else {
        arrivalTimeSection.style.display = 'none';
        absenceReasonSection.style.display = 'block';
    }
    
    updateStats();
}

function markAllPresent() {
    const statusSelects = document.querySelectorAll('.attendance-status');
    statusSelects.forEach(select => {
        select.value = 'present';
        updateStudentCard(select);
    });
}

function markAllAbsent() {
    const statusSelects = document.querySelectorAll('.attendance-status');
    statusSelects.forEach(select => {
        select.value = 'absent';
        updateStudentCard(select);
    });
}

function searchStudents() {
    const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
    const studentCards = document.querySelectorAll('.student-card');
    
    studentCards.forEach(card => {
        const studentName = card.getAttribute('data-student-name');
        if (studentName.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function updateStats() {
    const statusSelects = document.querySelectorAll('.attendance-status');
    let presentCount = 0;
    let absentCount = 0;
    let totalCount = statusSelects.length;
    
    statusSelects.forEach(select => {
        if (select.value === 'present' || select.value === 'late') {
            presentCount++;
        } else {
            absentCount++;
        }
    });
    
    const percentage = totalCount > 0 ? Math.round((presentCount / totalCount) * 100) : 0;
    
    // Update stats display
    document.querySelector('.stat-circle .stat-number').textContent = presentCount;
    document.querySelector('.stat-circle').style.setProperty('--percentage', percentage);
    
    const statItems = document.querySelectorAll('.stat-item .stat-number');
    if (statItems.length >= 2) {
        statItems[0].textContent = absentCount;
        statItems[1].textContent = percentage + '%';
    }
}

function saveAttendance() {
    if (confirm('هل أنت متأكد من حفظ بيانات الحضور؟')) {
        document.getElementById('attendanceForm').submit();
    }
}

function resetForm() {
    if (confirm('هل أنت متأكد من إعادة تعيين النموذج؟ سيتم فقدان جميع التغييرات غير المحفوظة.')) {
        location.reload();
    }
}

// Initialize stats on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
    
    // Set CSS custom property for initial percentage
    const statCircle = document.querySelector('.stat-circle');
    if (statCircle) {
        const percentage = statCircle.getAttribute('data-percentage') || 0;
        statCircle.style.setProperty('--percentage', percentage);
    }
});

// Auto-save functionality (optional)
let autoSaveTimer;
function scheduleAutoSave() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        // Auto-save logic here if needed
        console.log('Auto-save triggered');
    }, 30000); // 30 seconds
}

// Listen for form changes
document.addEventListener('change', function(e) {
    if (e.target.closest('#attendanceForm')) {
        scheduleAutoSave();
    }
});
</script>
@endpush

