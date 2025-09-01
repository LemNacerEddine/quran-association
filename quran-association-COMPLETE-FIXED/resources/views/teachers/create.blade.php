@extends('layouts.dashboard')

@section('title', 'إضافة معلم جديد')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-tie text-info me-2"></i>
            إضافة معلم جديد
        </h1>
        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">بيانات المعلم</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('teachers.store') }}">
                @csrf
                
                <!-- المعلومات الأساسية -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">الاسم الكامل *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">رقم الهاتف *</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" 
                               placeholder="0699674964" required>
                        <small class="form-text text-muted">سيتم إنشاء كود الدخول من آخر 4 أرقام</small>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="birth_date" class="form-label">تاريخ الميلاد</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                               id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="hire_date" class="form-label">تاريخ التوظيف</label>
                        <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                               id="hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}">
                        <small class="form-text text-muted">سيتم تعيين تاريخ اليوم تلقائياً إذا لم يتم تحديده</small>
                        @error('hire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- المعلومات الإضافية (اختيارية) -->
                <hr class="my-4">
                <h6 class="text-muted mb-3">معلومات إضافية (اختيارية)</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">الجنس</label>
                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">اختر الجنس</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="specialization" class="form-label">التخصص</label>
                        <input type="text" class="form-control @error('specialization') is-invalid @enderror" 
                               id="specialization" name="specialization" value="{{ old('specialization') }}">
                        @error('specialization')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="qualification" class="form-label">المؤهل العلمي</label>
                        <input type="text" class="form-control @error('qualification') is-invalid @enderror" 
                               id="qualification" name="qualification" value="{{ old('qualification') }}">
                        @error('qualification')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="experience_years" class="form-label">سنوات الخبرة</label>
                        <input type="number" class="form-control @error('experience_years') is-invalid @enderror" 
                               id="experience_years" name="experience_years" min="0" max="50" value="{{ old('experience_years') }}">
                        @error('experience_years')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="salary" class="form-label">الراتب</label>
                        <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                               id="salary" name="salary" step="0.01" min="0" value="{{ old('salary') }}">
                        @error('salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-2"></i>حفظ البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

