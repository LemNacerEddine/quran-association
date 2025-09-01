@extends('layouts.dashboard')

@section('title', 'قائمة المعلمين')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chalkboard-teacher text-info me-2"></i>
            قائمة المعلمين
        </h1>
        <a href="{{ route('teachers.create') }}" class="btn btn-info">
            <i class="fas fa-plus me-2"></i>إضافة معلم جديد
        </a>
    </div>

    <!-- Desktop Table View -->
    <div class="card shadow mb-4 d-none d-lg-block">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">جميع المعلمين</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>التخصص</th>
                            <th>سنوات الخبرة</th>
                            <th>عدد الحلقات</th>
                            <th>الحالة</th>
                            <th>تاريخ التوظيف</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="avatar-initial bg-info rounded-circle">
                                            {{ substr($teacher->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $teacher->name }}</strong>
                                        @if($teacher->phone)
                                            <br><small class="text-muted">{{ $teacher->phone }}</small>
                                        @endif
                                        @if($teacher->email)
                                            <br><small class="text-muted">{{ $teacher->email }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($teacher->specialization)
                                    <span class="badge bg-primary text-white">{{ $teacher->specialization }}</span>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                @if($teacher->experience_years)
                                    <span class="badge bg-success text-white">{{ $teacher->experience_years }} سنة</span>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                @if($teacher->circles && $teacher->circles->count() > 0)
                                    <span class="badge bg-info text-white">{{ $teacher->circles->count() }} حلقة</span>
                                    <br>
                                    @foreach($teacher->circles as $circle)
                                        <small class="text-muted">{{ $circle->name }}</small><br>
                                    @endforeach
                                @else
                                    <span class="badge bg-warning text-dark">لا توجد حلقات</span>
                                @endif
                            </td>
                            <td>
                                @if($teacher->is_active)
                                    <span class="badge bg-success text-white">نشط</span>
                                @else
                                    <span class="badge bg-danger text-white">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                {{ $teacher->hire_date ? \Carbon\Carbon::parse($teacher->hire_date)->format('Y-m-d') : ($teacher->created_at ? $teacher->created_at->format('Y-m-d') : 'غير محدد') }}
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد معلمين مسجلين</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="d-lg-none">
        @forelse($teachers as $teacher)
        <div class="card shadow mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <div class="avatar-initial bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; color: white;">
                            {{ substr($teacher->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="col-9">
                        <h5 class="card-title mb-1">{{ $teacher->name }}</h5>
                        @if($teacher->phone)
                            <p class="text-muted mb-1"><i class="fas fa-phone me-1"></i>{{ $teacher->phone }}</p>
                        @endif
                        @if($teacher->email)
                            <p class="text-muted mb-2"><i class="fas fa-envelope me-1"></i>{{ $teacher->email }}</p>
                        @endif
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>التخصص:</strong>
                    </div>
                    <div class="col-6">
                        @if($teacher->specialization)
                            <span class="badge bg-primary text-white">{{ $teacher->specialization }}</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>سنوات الخبرة:</strong>
                    </div>
                    <div class="col-6">
                        @if($teacher->experience_years)
                            <span class="badge bg-success text-white">{{ $teacher->experience_years }} سنة</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>عدد الحلقات:</strong>
                    </div>
                    <div class="col-6">
                        @if($teacher->circles && $teacher->circles->count() > 0)
                            <span class="badge bg-info text-white">{{ $teacher->circles->count() }} حلقة</span>
                            <br>
                            @foreach($teacher->circles as $circle)
                                <small class="text-muted">{{ $circle->name }}</small><br>
                            @endforeach
                        @else
                            <span class="badge bg-warning text-dark">لا توجد حلقات</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>الحالة:</strong>
                    </div>
                    <div class="col-6">
                        @if($teacher->is_active)
                            <span class="badge bg-success text-white">نشط</span>
                        @else
                            <span class="badge bg-danger text-white">غير نشط</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>تاريخ التوظيف:</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">{{ $teacher->hire_date ? \Carbon\Carbon::parse($teacher->hire_date)->format('Y-m-d') : ($teacher->created_at ? $teacher->created_at->format('Y-m-d') : 'غير محدد') }}</small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye me-1"></i>عرض
                    </a>
                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-1"></i>تعديل
                    </a>
                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                            <i class="fas fa-trash me-1"></i>حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="card shadow">
            <div class="card-body text-center">
                <p class="text-muted">لا توجد معلمين مسجلين</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

