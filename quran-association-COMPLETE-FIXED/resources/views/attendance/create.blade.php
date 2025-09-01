@extends('layouts.dashboard')

@section('title', 'تسجيل حضور جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        تسجيل حضور جديد
                    </h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('attendance.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- اختيار الجلسة -->
                            <div class="col-md-6 mb-3">
                                <label for="session_id" class="form-label">الجلسة <span class="text-danger">*</span></label>
                                <select name="session_id" id="session_id" class="form-select" required>
                                    <option value="">اختر الجلسة</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->circle->name }} - 
                                            {{ $session->session_date->format('Y-m-d') }} 
                                            ({{ $session->start_time }} - {{ $session->end_time }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- اختيار الطالب -->
                            <div class="col-md-6 mb-3">
                                <label for="student_id" class="form-label">الطالب <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-select" required>
                                    <option value="">اختر الطالب</option>
                                    <!-- سيتم ملؤها عبر JavaScript حسب الجلسة المختارة -->
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- حالة الحضور -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">حالة الحضور <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="">اختر الحالة</option>
                                    <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>حاضر</option>
                                    <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>غائب</option>
                                    <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>متأخر</option>
                                    <option value="excused" {{ old('status') == 'excused' ? 'selected' : '' }}>معذور</option>
                                </select>
                            </div>

                            <!-- ملاحظات -->
                            <div class="col-md-6 mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-2"></i>
                                العودة
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>
                                تسجيل الحضور
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- الجلسات القادمة -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        الجلسات القادمة
                    </h5>
                </div>
                <div class="card-body">
                    @if($sessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>الحلقة</th>
                                        <th>المعلم</th>
                                        <th>التاريخ</th>
                                        <th>الوقت</th>
                                        <th>عدد الطلاب</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                        <tr>
                                            <td>{{ $session->circle->name }}</td>
                                            <td>{{ $session->circle->teacher->name ?? 'غير محدد' }}</td>
                                            <td>{{ $session->session_date->format('Y-m-d') }}</td>
                                            <td>{{ $session->start_time }} - {{ $session->end_time }}</td>
                                            <td>{{ $session->circle->students->count() }}</td>
                                            <td>
                                                <a href="{{ route('attendance.session', $session) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-users me-1"></i>
                                                    تسجيل جماعي
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">لا توجد جلسات قادمة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sessionSelect = document.getElementById('session_id');
    const studentSelect = document.getElementById('student_id');
    
    sessionSelect.addEventListener('change', function() {
        const sessionId = this.value;
        studentSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        
        if (sessionId) {
            // هنا يمكن إضافة AJAX call لجلب طلاب الحلقة
            // مؤقتاً سنضع خيار عام
            studentSelect.innerHTML = '<option value="">اختر الطالب</option>';
        } else {
            studentSelect.innerHTML = '<option value="">اختر الطالب</option>';
        }
    });
});
</script>
@endsection

