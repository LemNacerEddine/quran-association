@extends('layouts.guardian')

@section('title', 'الملف الشخصي - ' . $guardian->name)

@section('content')
<div class="container-fluid py-4">
    <!-- Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">
                                <i class="fas fa-user me-2"></i>
                                الملف الشخصي
                            </h2>
                            <p class="mb-0 opacity-75">
                                إدارة بياناتك الشخصية ومعلومات الاتصال
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-user-circle fa-4x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        المعلومات الشخصية
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('guardian.profile.update') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">الاسم الكامل</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $guardian->name) }}" required>
                                @error('name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', $guardian->phone) }}" required readonly>
                                <small class="text-muted">لا يمكن تغيير رقم الهاتف</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $guardian->email) }}">
                                @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="national_id" class="form-label">رقم الهوية</label>
                                <input type="text" class="form-control" id="national_id" name="national_id" 
                                       value="{{ old('national_id', $guardian->national_id) }}">
                                @error('national_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="occupation" class="form-label">المهنة</label>
                                <input type="text" class="form-control" id="occupation" name="occupation" 
                                       value="{{ old('occupation', $guardian->occupation) }}">
                                @error('occupation')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="relationship" class="form-label">صلة القرابة</label>
                                <select class="form-select" id="relationship" name="relationship" required>
                                    <option value="">اختر صلة القرابة</option>
                                    <option value="father" {{ old('relationship', $guardian->relationship) == 'father' ? 'selected' : '' }}>الأب</option>
                                    <option value="mother" {{ old('relationship', $guardian->relationship) == 'mother' ? 'selected' : '' }}>الأم</option>
                                    <option value="guardian" {{ old('relationship', $guardian->relationship) == 'guardian' ? 'selected' : '' }}>ولي الأمر</option>
                                    <option value="other" {{ old('relationship', $guardian->relationship) == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('relationship')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $guardian->address) }}</textarea>
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                حفظ التغييرات
                            </button>
                            <a href="{{ route('guardian.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                العودة للوحة التحكم
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Account Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        معلومات الحساب
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">رقم الهاتف</small>
                        <div class="fw-bold">{{ $guardian->phone }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">كود الدخول</small>
                        <div class="fw-bold font-monospace">{{ $guardian->access_code }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">آخر دخول</small>
                        <div class="fw-bold">
                            {{ $guardian->last_login_at ? $guardian->last_login_at->format('Y-m-d H:i') : 'أول مرة' }}
                        </div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">حالة الحساب</small>
                        <span class="badge bg-{{ $guardian->is_active ? 'success' : 'danger' }}">
                            {{ $guardian->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Students Summary -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        ملخص الأولاد
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">إجمالي الأولاد</small>
                        <div class="fw-bold text-primary">{{ $guardian->students->count() }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">أولاد أساسيين</small>
                        <div class="fw-bold text-warning">{{ $guardian->students->where('pivot.is_primary', true)->count() }}</div>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">في حلقات نشطة</small>
                        <div class="fw-bold text-success">{{ $guardian->students->whereNotNull('circle')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

