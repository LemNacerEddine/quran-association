@extends('layouts.dashboard')

@section('title', 'إدارة أولياء الأمور')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-primary mb-1">
                        <i class="fas fa-users-cog me-2"></i>
                        إدارة أولياء الأمور
                    </h2>
                    <p class="text-muted mb-0">إدارة بيانات أولياء الأمور وربطهم بالطلاب</p>
                </div>
                <a href="{{ route('guardians.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    إضافة ولي أمر جديد
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                    <small>إجمالي أولياء الأمور</small>
                                </div>
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0">{{ $stats['active'] }}</h4>
                                    <small>نشط</small>
                                </div>
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0">{{ $stats['fathers'] }}</h4>
                                    <small>آباء</small>
                                </div>
                                <i class="fas fa-male fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0">{{ $stats['mothers'] }}</h4>
                                    <small>أمهات</small>
                                </div>
                                <i class="fas fa-female fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('guardians.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">البحث</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="البحث بالاسم، الهاتف، البريد..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">صلة القرابة</label>
                            <select name="relationship" class="form-select">
                                <option value="">جميع الأنواع</option>
                                <option value="father" {{ request('relationship') === 'father' ? 'selected' : '' }}>الأب</option>
                                <option value="mother" {{ request('relationship') === 'mother' ? 'selected' : '' }}>الأم</option>
                                <option value="guardian" {{ request('relationship') === 'guardian' ? 'selected' : '' }}>ولي الأمر</option>
                                <option value="other" {{ request('relationship') === 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('guardians.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Guardians Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    @if($guardians->count() > 0)
                        <!-- Desktop Table -->
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>رقم الهاتف</th>
                                        <th>صلة القرابة</th>
                                        <th>عدد الأولاد</th>
                                        <th>كود الدخول</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($guardians as $guardian)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $guardian->name }}</h6>
                                                    @if($guardian->email)
                                                        <small class="text-muted">{{ $guardian->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-primary">{{ $guardian->phone }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $guardian->relationship_text }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $guardian->students->count() }} طالب</span>
                                        </td>
                                        <td>
                                            <code class="bg-light text-dark px-2 py-1 rounded">{{ $guardian->access_code }}</code>
                                        </td>
                                        <td>
                                            @if($guardian->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('guardians.show', $guardian) }}" 
                                                   class="btn btn-sm btn-outline-info" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('guardians.edit', $guardian) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('guardians.destroy', $guardian) }}" 
                                                      class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف ولي الأمر؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="d-lg-none">
                            @foreach($guardians as $guardian)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">{{ $guardian->name }}</h6>
                                        @if($guardian->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </div>
                                    
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block">رقم الهاتف</small>
                                            <span class="text-primary">{{ $guardian->phone }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">صلة القرابة</small>
                                            <span class="badge bg-info">{{ $guardian->relationship_text }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">عدد الأولاد</small>
                                            <span class="badge bg-success">{{ $guardian->students->count() }} طالب</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">كود الدخول</small>
                                            <code class="bg-light text-dark px-2 py-1 rounded">{{ $guardian->access_code }}</code>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('guardians.show', $guardian) }}" 
                                           class="btn btn-sm btn-outline-info flex-fill">
                                            <i class="fas fa-eye me-1"></i> عرض
                                        </a>
                                        <a href="{{ route('guardians.edit', $guardian) }}" 
                                           class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="fas fa-edit me-1"></i> تعديل
                                        </a>
                                        <form method="POST" action="{{ route('guardians.destroy', $guardian) }}" 
                                              class="flex-fill" onsubmit="return confirm('هل أنت متأكد من حذف ولي الأمر؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="fas fa-trash me-1"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $guardians->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد أولياء أمور</h5>
                            <p class="text-muted">لم يتم العثور على أولياء أمور مطابقين للبحث</p>
                            <a href="{{ route('guardians.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                إضافة ولي أمر جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
}

@media (max-width: 768px) {
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>
@endsection

