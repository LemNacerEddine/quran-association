@extends("layouts.dashboard")

@section("title", "تعديل المعلم")

@section("content")
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-edit text-info me-2"></i>
            تعديل المعلم: {{ $teacher->name }}
        </h1>
        <a href="{{ route("teachers.index") }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>العودة
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">بيانات المعلم</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route("teachers.update", $teacher) }}">
                @csrf
                @method("PUT")
                
                <!-- المعلومات الأساسية -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">الاسم الكامل *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $teacher->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">رقم الهاتف *</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $teacher->phone) }}" 
                               placeholder="0699674964" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">كود الدخول *</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" 
                               value="{{ old('password', $teacher->password) }}" 
                               placeholder="4 أرقام" maxlength="4" required>
                        <small class="form-text text-muted">يستخدم لتسجيل الدخول مع رقم الهاتف</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="birth_date" class="form-label">تاريخ الميلاد</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                               id="birth_date" name="birth_date" 
                               value="{{ old('birth_date', $teacher->birth_date) }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- المعلومات الإضافية -->
                <hr class="my-4">
                <h6 class="text-muted mb-3">معلومات إضافية (اختيارية)</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $teacher->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">الجنس</label>
                        <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender">
                            <option value="">اختر الجنس</option>
                            <option value="male" {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
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
                               id="specialization" name="specialization" 
                               value="{{ old('specialization', $teacher->specialization) }}">
                        @error('specialization')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="qualification" class="form-label">المؤهل العلمي</label>
                        <input type="text" class="form-control @error('qualification') is-invalid @enderror" 
                               id="qualification" name="qualification" 
                               value="{{ old('qualification', $teacher->qualification) }}">
                        @error('qualification')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="experience_years" class="form-label">سنوات الخبرة</label>
                        <input type="number" class="form-control @error('experience_years') is-invalid @enderror" 
                               id="experience_years" name="experience_years" min="0" max="50" 
                               value="{{ old('experience_years', $teacher->experience_years) }}">
                        @error('experience_years')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="salary" class="form-label">الراتب</label>
                        <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                               id="salary" name="salary" step="0.01" min="0" 
                               value="{{ old('salary', $teacher->salary) }}">
                        @error('salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3">{{ old('address', $teacher->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- الحالة -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" 
                                   id="is_active" name="is_active" value="1" 
                                   {{ old("is_active", $teacher->is_active) ? "checked" : "" }}>
                            <label class="form-check-label" for="is_active">
                                <strong>المعلم نشط</strong>
                            </label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-2"></i>حفظ التغييرات
                    </button>
                    <a href="{{ route("teachers.index") }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

