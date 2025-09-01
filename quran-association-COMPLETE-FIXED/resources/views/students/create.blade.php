@extends('layouts.dashboard')

@section('title', 'إضافة طالب جديد')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus text-success me-2"></i>
            إضافة طالب جديد
        </h1>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">بيانات الطالب</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('students.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">الاسم الكامل</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date_of_birth" class="form-label">تاريخ الميلاد</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">الجنس</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">اختر الجنس</option>
                            <option value="male">ذكر</option>
                            <option value="female">أنثى</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>حفظ البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

