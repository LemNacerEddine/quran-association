@extends('layouts.dashboard')

@section('title', 'إضافة حلقة جديدة')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-success me-2"></i>
            إضافة حلقة جديدة
        </h1>
        <a href="{{ route('circles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">بيانات الحلقة</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('circles.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">اسم الحلقة</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="teacher_id" class="form-label">المعلم</label>
                        <select class="form-control" id="teacher_id" name="teacher_id" required>
                            <option value="">اختر المعلم</option>
                            <!-- سيتم ملؤها من قاعدة البيانات -->
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="level" class="form-label">المستوى</label>
                        <select class="form-control" id="level" name="level" required>
                            <option value="">اختر المستوى</option>
                            <option value="مبتدئ">مبتدئ</option>
                            <option value="متوسط">متوسط</option>
                            <option value="متقدم">متقدم</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="max_students" class="form-label">الحد الأقصى للطلاب</label>
                        <input type="number" class="form-control" id="max_students" name="max_students" min="1" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="age_group" class="form-label">الفئة العمرية</label>
                        <select class="form-control" id="age_group" name="age_group" required>
                            <option value="">اختر الفئة العمرية</option>
                            <option value="أطفال">أطفال (5-12 سنة)</option>
                            <option value="شباب">شباب (13-25 سنة)</option>
                            <option value="كبار">كبار (26+ سنة)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">المكان</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">وصف الحلقة</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>

                <!-- قسم تحديد الطلاب -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-users me-2"></i>تحديد الطلاب في الحلقة
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">اختر الطلاب:</label>
                                <div class="form-check-container" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                                    @if($students && $students->count() > 0)
                                        @foreach($students as $student)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="students[]" value="{{ $student->id }}" id="student_{{ $student->id }}">
                                                <label class="form-check-label" for="student_{{ $student->id }}">
                                                    <strong>{{ $student->name }}</strong>
                                                    <small class="text-muted">({{ $student->phone ?? 'لا يوجد رقم' }})</small>
                                                    @if($student->gender)
                                                        <span class="badge bg-{{ $student->gender == 'male' ? 'primary' : 'pink' }} ms-2">
                                                            {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}
                                                        </span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">لا توجد طلاب متاحون للتسجيل</p>
                                    @endif
                                </div>
                                <small class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    يمكنك اختيار عدة طلاب للحلقة الواحدة. يمكن للطالب الواحد التسجيل في حلقات متعددة.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>حفظ البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

