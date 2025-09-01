@extends('layouts.dashboard')

@section('title', 'تفاصيل ولي الأمر - ' . $guardian->name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-user me-2"></i>
                        {{ $guardian->name }}
                    </h2>
                    <p class="text-muted mb-0">تفاصيل ولي الأمر ومعلومات الأولاد</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('guardians.edit', $guardian) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        تعديل
                    </a>
                    <a href="{{ route('guardians.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <!-- Guardian Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات ولي الأمر
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-user me-1"></i>
                                الاسم الكامل
                            </h6>
                            <p class="mb-0">{{ $guardian->name }}</p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-phone me-1"></i>
                                رقم الهاتف
                            </h6>
                            <p class="mb-0">
                                <a href="tel:{{ $guardian->phone }}" class="text-decoration-none">
                                    {{ $guardian->phone }}
                                </a>
                            </p>
                        </div>
                        
                        @if($guardian->email)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-envelope me-1"></i>
                                البريد الإلكتروني
                            </h6>
                            <p class="mb-0">
                                <a href="mailto:{{ $guardian->email }}" class="text-decoration-none">
                                    {{ $guardian->email }}
                                </a>
                            </p>
                        </div>
                        @endif
                        
                        @if($guardian->national_id)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-id-card me-1"></i>
                                رقم الهوية
                            </h6>
                            <p class="mb-0">{{ $guardian->national_id }}</p>
                        </div>
                        @endif
                        
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-heart me-1"></i>
                                صلة القرابة
                            </h6>
                            <span class="badge bg-info">{{ $guardian->relationship_text }}</span>
                        </div>
                        
                        @if($guardian->job)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-briefcase me-1"></i>
                                المهنة
                            </h6>
                            <p class="mb-0">{{ $guardian->job }}</p>
                        </div>
                        @endif
                        
                        @if($guardian->address)
                        <div class="col-12 mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                العنوان
                            </h6>
                            <p class="mb-0">{{ $guardian->address }}</p>
                        </div>
                        @endif
                        
                        @if($guardian->notes)
                        <div class="col-12 mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-sticky-note me-1"></i>
                                ملاحظات
                            </h6>
                            <p class="mb-0">{{ $guardian->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        الأولاد المسجلين ({{ $guardian->students->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($guardian->students->count() > 0)
                        <div class="row">
                            @foreach($guardian->students as $student)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">{{ $student->name }}</h6>
                                            @if($student->pivot->is_primary)
                                                <span class="badge bg-warning">أساسي</span>
                                            @endif
                                        </div>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">نوع العلاقة:</small>
                                            <span class="badge bg-info ms-1">
                                                @switch($student->pivot->relationship_type)
                                                    @case('father') الأب @break
                                                    @case('mother') الأم @break
                                                    @case('guardian') ولي الأمر @break
                                                    @default أخرى
                                                @endswitch
                                            </span>
                                        </div>
                                        
                                        @if($student->circle)
                                        <div class="mb-2">
                                            <small class="text-muted">الحلقة:</small>
                                            <div class="text-primary">{{ $student->circle->name }}</div>
                                            @if($student->circle->teacher)
                                                <small class="text-muted">المعلم: {{ $student->circle->teacher->name }}</small>
                                            @endif
                                        </div>
                                        @else
                                        <div class="mb-2">
                                            <small class="text-muted">غير مسجل في حلقة</small>
                                        </div>
                                        @endif
                                        
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="{{ route('students.show', $student) }}" 
                                               class="btn btn-sm btn-outline-primary flex-fill">
                                                <i class="fas fa-eye me-1"></i> عرض
                                            </a>
                                            <a href="{{ route('students.edit', $student) }}" 
                                               class="btn btn-sm btn-outline-success flex-fill">
                                                <i class="fas fa-edit me-1"></i> تعديل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد أولاد مسجلين</h5>
                            <p class="text-muted">لم يتم ربط أي طلاب بولي الأمر هذا</p>
                            <a href="{{ route('guardians.edit', $guardian) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                ربط طلاب
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Access Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        معلومات الدخول
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-phone me-1"></i>
                            رقم الهاتف للدخول
                        </h6>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $guardian->phone }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $guardian->phone }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-lock me-1"></i>
                            كود الدخول
                        </h6>
                        <div class="input-group">
                            <input type="text" class="form-control text-center fw-bold" value="{{ $guardian->access_code }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $guardian->access_code }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <form method="POST" action="{{ route('guardians.resetAccessCode', $guardian) }}" 
                              onsubmit="return confirm('هل أنت متأكد من إعادة تعيين كود الدخول؟')">
                            @csrf
                            <button type="submit" class="btn btn-outline-warning">
                                <i class="fas fa-sync me-2"></i>
                                إعادة تعيين الكود
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        الحالة والإحصائيات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $guardian->students->count() }}</h4>
                                <small class="text-muted">إجمالي الأولاد</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-1">{{ $guardian->students->where('pivot.is_primary', true)->count() }}</h4>
                            <small class="text-muted">أولاد أساسيين</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">الحالة</h6>
                        @if($guardian->is_active)
                            <span class="badge bg-success">نشط</span>
                        @else
                            <span class="badge bg-secondary">غير نشط</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">تاريخ التسجيل</h6>
                        <small class="text-muted">{{ $guardian->created_at->format('Y-m-d H:i') }}</small>
                    </div>
                    
                    <div class="d-grid">
                        <form method="POST" action="{{ route('guardians.toggleStatus', $guardian) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $guardian->is_active ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $guardian->is_active ? 'pause' : 'play' }} me-2"></i>
                                {{ $guardian->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('guardians.edit', $guardian) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>
                            تعديل البيانات
                        </a>
                        
                        <a href="tel:{{ $guardian->phone }}" class="btn btn-outline-success">
                            <i class="fas fa-phone me-2"></i>
                            اتصال هاتفي
                        </a>
                        
                        @if($guardian->email)
                        <a href="mailto:{{ $guardian->email }}" class="btn btn-outline-info">
                            <i class="fas fa-envelope me-2"></i>
                            إرسال بريد
                        </a>
                        @endif
                        
                        <form method="POST" action="{{ route('guardians.destroy', $guardian) }}" 
                              onsubmit="return confirm('هل أنت متأكد من حذف ولي الأمر؟ سيتم حذف جميع الروابط مع الطلاب.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-2"></i>
                                حذف ولي الأمر
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    تم نسخ النص بنجاح!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    });
}
</script>

<style>
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .d-flex.gap-2 {
        flex-direction: column;
    }
    
    .d-flex.gap-2 > * {
        margin-bottom: 0.5rem;
    }
}
</style>
@endsection

