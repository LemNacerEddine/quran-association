@extends('layouts.dashboard')

@section('title', 'تعديل الجلسة')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل الجلسة</h1>
        <div>
            <a href="{{ route('sessions.show', $session) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> عرض الجلسة
            </a>
            <a href="{{ route('sessions.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تعديل معلومات الجلسة</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('sessions.update', $session) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="session_title" class="form-label">عنوان الجلسة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('session_title') is-invalid @enderror" 
                                       id="session_title" name="session_title" 
                                       value="{{ old('session_title', $session->session_title) }}" required>
                                @error('session_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="session_date" class="form-label">تاريخ الجلسة <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('session_date') is-invalid @enderror" 
                                       id="session_date" name="session_date" 
                                       value="{{ old('session_date', $session->session_date) }}" required>
                                @error('session_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="circle_id" class="form-label">الحلقة <span class="text-danger">*</span></label>
                                <select class="form-control @error('circle_id') is-invalid @enderror" 
                                        id="circle_id" name="circle_id" required>
                                    <option value="">اختر الحلقة</option>
                                    @foreach($circles as $circle)
                                        <option value="{{ $circle->id }}" 
                                                {{ old('circle_id', $session->circle_id) == $circle->id ? 'selected' : '' }}>
                                            {{ $circle->name }} - {{ $circle->teacher->name ?? 'بدون معلم' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('circle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="schedule_id" class="form-label">الجدولة</label>
                                <select class="form-control @error('schedule_id') is-invalid @enderror" 
                                        id="schedule_id" name="schedule_id">
                                    <option value="">بدون جدولة محددة</option>
                                    @foreach($schedules as $schedule)
                                        <option value="{{ $schedule->id }}" 
                                                {{ old('schedule_id', $session->schedule_id) == $schedule->id ? 'selected' : '' }}>
                                            {{ $schedule->schedule_name }} - {{ $schedule->circle->name ?? 'غير محدد' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('schedule_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="actual_start_time" class="form-label">وقت البداية الفعلي</label>
                                <input type="time" class="form-control @error('actual_start_time') is-invalid @enderror" 
                                       id="actual_start_time" name="actual_start_time" 
                                       value="{{ old('actual_start_time', $session->actual_start_time) }}">
                                @error('actual_start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="actual_end_time" class="form-label">وقت النهاية الفعلي</label>
                                <input type="time" class="form-control @error('actual_end_time') is-invalid @enderror" 
                                       id="actual_end_time" name="actual_end_time" 
                                       value="{{ old('actual_end_time', $session->actual_end_time) }}">
                                @error('actual_end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">حالة الجلسة <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="scheduled" {{ old('status', $session->status) == 'scheduled' ? 'selected' : '' }}>
                                        مجدولة
                                    </option>
                                    <option value="ongoing" {{ old('status', $session->status) == 'ongoing' ? 'selected' : '' }}>
                                        جارية
                                    </option>
                                    <option value="completed" {{ old('status', $session->status) == 'completed' ? 'selected' : '' }}>
                                        مكتملة
                                    </option>
                                    <option value="cancelled" {{ old('status', $session->status) == 'cancelled' ? 'selected' : '' }}>
                                        ملغية
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="attendance_taken" class="form-label">تسجيل الحضور</label>
                                <select class="form-control @error('attendance_taken') is-invalid @enderror" 
                                        id="attendance_taken" name="attendance_taken">
                                    <option value="0" {{ old('attendance_taken', $session->attendance_taken) == '0' ? 'selected' : '' }}>
                                        لم يتم بعد
                                    </option>
                                    <option value="1" {{ old('attendance_taken', $session->attendance_taken) == '1' ? 'selected' : '' }}>
                                        تم التسجيل
                                    </option>
                                </select>
                                @error('attendance_taken')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="session_description" class="form-label">وصف الجلسة</label>
                                <textarea class="form-control @error('session_description') is-invalid @enderror" 
                                          id="session_description" name="session_description" rows="3"
                                          placeholder="وصف مختصر للجلسة...">{{ old('session_description', $session->session_description) }}</textarea>
                                @error('session_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="lesson_content" class="form-label">محتوى الدرس</label>
                                <textarea class="form-control @error('lesson_content') is-invalid @enderror" 
                                          id="lesson_content" name="lesson_content" rows="4"
                                          placeholder="محتوى الدرس والمواضيع المغطاة...">{{ old('lesson_content', $session->lesson_content) }}</textarea>
                                @error('lesson_content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="homework" class="form-label">الواجب المنزلي</label>
                                <textarea class="form-control @error('homework') is-invalid @enderror" 
                                          id="homework" name="homework" rows="3"
                                          placeholder="الواجبات والمهام المطلوبة من الطلاب...">{{ old('homework', $session->homework) }}</textarea>
                                @error('homework')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="session_notes" class="form-label">ملاحظات الجلسة</label>
                                <textarea class="form-control @error('session_notes') is-invalid @enderror" 
                                          id="session_notes" name="session_notes" rows="3"
                                          placeholder="ملاحظات إضافية حول الجلسة...">{{ old('session_notes', $session->session_notes) }}</textarea>
                                @error('session_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if($session->status === 'cancelled')
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="cancellation_reason" class="form-label">سبب الإلغاء</label>
                                <textarea class="form-control @error('cancellation_reason') is-invalid @enderror" 
                                          id="cancellation_reason" name="cancellation_reason" rows="2"
                                          placeholder="سبب إلغاء الجلسة...">{{ old('cancellation_reason', $session->cancellation_reason) }}</textarea>
                                @error('cancellation_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> حفظ التغييرات
                                        </button>
                                        <a href="{{ route('sessions.show', $session) }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> إلغاء
                                        </a>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            آخر تحديث: {{ $session->updated_at ? $session->updated_at->format('Y-m-d H:i') : 'غير محدد' }}
                                        </small>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide cancellation reason field if status is not cancelled
    const statusSelect = document.getElementById('status');
    const cancellationReasonRow = document.querySelector('#cancellation_reason').closest('.row');
    
    function toggleCancellationReason() {
        if (statusSelect.value === 'cancelled') {
            cancellationReasonRow.style.display = 'block';
        } else {
            cancellationReasonRow.style.display = 'none';
        }
    }
    
    statusSelect.addEventListener('change', toggleCancellationReason);
    toggleCancellationReason(); // Initial check
});
</script>
@endpush
@endsection

