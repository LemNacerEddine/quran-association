@extends('layouts.dashboard')

@section('title', 'قائمة الحلقات')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users text-success me-2"></i>
            قائمة الحلقات
        </h1>
        <a href="{{ route('circles.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>إضافة حلقة جديدة
        </a>
    </div>

    <!-- Desktop Table View -->
    <div class="card shadow mb-4 d-none d-lg-block">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">جميع الحلقات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>اسم الحلقة</th>
                            <th>المعلم</th>
                            <th>المستوى</th>
                            <th>عدد الطلاب</th>
                            <th>الحد الأقصى</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($circles as $circle)
                        <tr>
                            <td>
                                <div>
                                    <strong>{{ $circle->name }}</strong>
                                    @if($circle->description)
                                        <br><small class="text-muted">{{ $circle->description }}</small>
                                    @endif
                                    @if($circle->location)
                                        <br><small class="text-info"><i class="fas fa-map-marker-alt"></i> {{ $circle->location }}</small>
                                    @endif
                                      @if($circle->schedule_days)
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar"></i> {{ $circle->schedule_days }}
                                        </small>
                                    @endif
                                    @if($circle->start_time && $circle->end_time)
                                        <br><small class="text-warning">
                                            <i class="fas fa-clock"></i> 
                                            {{ \Carbon\Carbon::parse($circle->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($circle->end_time)->format('H:i') }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($circle->teacher)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-2">
                                            <div class="avatar-initial bg-info rounded-circle" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ substr($circle->teacher->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-info text-white">{{ $circle->teacher->name }}</span>
                                            @if($circle->teacher->specialization)
                                                <br><small class="text-muted">{{ $circle->teacher->specialization }}</small>
                                            @endif
                                            @if($circle->teacher->experience_years)
                                                <br><small class="text-success">{{ $circle->teacher->experience_years }} سنة خبرة</small>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary text-white">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    @if($circle->level)
                                        @php
                                            $levelNames = [
                                                'beginner' => 'مبتدئ',
                                                'intermediate' => 'متوسط', 
                                                'advanced' => 'متقدم'
                                            ];
                                        @endphp
                                        <span class="badge bg-primary text-white">{{ $levelNames[$circle->level] ?? $circle->level }}</span>
                                    @else
                                        <span class="badge bg-secondary text-white">غير محدد</span>
                                    @endif
                                    @if($circle->age_group)
                                        <br><small class="text-muted">{{ $circle->age_group }}</small>
                                    @endif
                                    @if($circle->gender)
                                        <br><small class="text-info">
                                            @if($circle->gender == 'male')
                                                <i class="fas fa-mars"></i> ذكور
                                            @elseif($circle->gender == 'female')
                                                <i class="fas fa-venus"></i> إناث
                                            @else
                                                <i class="fas fa-venus-mars"></i> مختلط
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <span class="badge badge-success" style="font-size: 14px;">{{ $circle->students->count() }}</span>
                                    @if($circle->students->count() > 0)
                                        <br><small class="text-muted">
                                            @foreach($circle->students->take(3) as $student)
                                                {{ $student->name }}@if(!$loop->last), @endif
                                            @endforeach
                                            @if($circle->students->count() > 3)
                                                وآخرون...
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <span class="badge badge-warning" style="font-size: 14px;">{{ $circle->max_students ?? 'غير محدد' }}</span>
                                    @if($circle->max_students)
                                        <br><small class="text-muted">
                                            @php
                                                $percentage = $circle->max_students > 0 ? round(($circle->students->count() / $circle->max_students) * 100) : 0;
                                            @endphp
                                            {{ $percentage }}% ممتلئة
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($circle->is_active)
                                    <span class="badge bg-success text-white">نشط</span>
                                @else
                                    <span class="badge bg-danger text-white">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('circles.show', $circle) }}" class="btn btn-sm btn-info" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('circles.edit', $circle) }}" class="btn btn-sm btn-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('circles.destroy', $circle) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger delete-btn" 
                                                data-circle-name="{{ $circle->name }}" 
                                                data-students-count="{{ $circle->students()->count() }}"
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد حلقات متاحة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="d-lg-none">
        @forelse($circles as $circle)
        <div class="card shadow mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-3">
                        <div class="avatar-initial bg-success rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; color: white;">
                            {{ substr($circle->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="col-9">
                        <h5 class="card-title mb-1">{{ $circle->name }}</h5>
                        @if($circle->description)
                            <p class="text-muted mb-1">{{ $circle->description }}</p>
                        @endif
                        @if($circle->location)
                            <p class="text-info mb-2"><i class="fas fa-map-marker-alt me-1"></i>{{ $circle->location }}</p>
                        @endif
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>المعلم:</strong>
                    </div>
                    <div class="col-6">
                        @if($circle->teacher)
                            <span class="badge bg-info text-white">{{ $circle->teacher->name }}</span>
                            @if($circle->teacher->specialization)
                                <br><small class="text-muted">{{ $circle->teacher->specialization }}</small>
                            @endif
                        @else
                            <span class="badge bg-secondary text-white">غير محدد</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>المستوى:</strong>
                    </div>
                    <div class="col-6">
                        @if($circle->level)
                            @php
                                $levelNames = [
                                    'beginner' => 'مبتدئ',
                                    'intermediate' => 'متوسط', 
                                    'advanced' => 'متقدم'
                                ];
                            @endphp
                            <span class="badge bg-primary text-white">{{ $levelNames[$circle->level] ?? $circle->level }}</span>
                        @else
                            <span class="badge bg-secondary text-white">غير محدد</span>
                        @endif
                        @if($circle->age_group)
                            <br><small class="text-muted">{{ $circle->age_group }}</small>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>عدد الطلاب:</strong>
                    </div>
                    <div class="col-6">
                        <span class="badge bg-success text-white">{{ $circle->students->count() }}</span>
                        @if($circle->students->count() > 0)
                            <br><small class="text-muted">
                                @foreach($circle->students->take(2) as $student)
                                    {{ $student->name }}@if(!$loop->last), @endif
                                @endforeach
                                @if($circle->students->count() > 2)
                                    وآخرون...
                                @endif
                            </small>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>الحد الأقصى:</strong>
                    </div>
                    <div class="col-6">
                        <span class="badge bg-warning text-dark">{{ $circle->max_students ?? 'غير محدد' }}</span>
                        @if($circle->max_students)
                            @php
                                $percentage = $circle->max_students > 0 ? round(($circle->students->count() / $circle->max_students) * 100) : 0;
                            @endphp
                            <br><small class="text-muted">{{ $percentage }}% ممتلئة</small>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <strong>الحالة:</strong>
                    </div>
                    <div class="col-6">
                        @if($circle->is_active)
                            <span class="badge bg-success text-white">نشط</span>
                        @else
                            <span class="badge bg-danger text-white">غير نشط</span>
                        @endif
                    </div>
                </div>
                
                @if($circle->schedule_days)
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>أيام الحلقة:</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-success">
                            <i class="fas fa-calendar me-1"></i>{{ $circle->schedule_days }}
                        </small>
                        @if($circle->start_time && $circle->end_time)
                            <br><small class="text-warning">
                                <i class="fas fa-clock me-1"></i>{{ $circle->start_time }} - {{ $circle->end_time }}
                            </small>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('circles.show', $circle) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye me-1"></i>عرض
                    </a>
                    <a href="{{ route('circles.edit', $circle) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-1"></i>تعديل
                    </a>
                    <form action="{{ route('circles.destroy', $circle) }}" method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger delete-btn" 
                                data-circle-name="{{ $circle->name }}" 
                                data-students-count="{{ $circle->students()->count() }}">
                            <i class="fas fa-trash me-1"></i>حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="card shadow">
            <div class="card-body text-center">
                <p class="text-muted">لا توجد حلقات متاحة</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // معالجة أزرار الحذف
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        
        const form = $(this).closest('.delete-form');
        const circleName = $(this).data('circle-name');
        const studentsCount = $(this).data('students-count');
        
        // رسالة التأكيد
        let confirmText = `هل أنت متأكد من حذف الحلقة "${circleName}"؟`;
        let warningText = '';
        
        if (studentsCount > 0) {
            warningText = `تحتوي هذه الحلقة على ${studentsCount} طالب. لا يمكن حذفها.`;
            
            Swal.fire({
                title: 'تعذر الحذف!',
                text: warningText,
                icon: 'warning',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#3085d6'
            });
            return;
        }
        
        Swal.fire({
            title: 'تأكيد الحذف',
            text: confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // إظهار مؤشر التحميل
                Swal.fire({
                    title: 'جاري الحذف...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // إرسال طلب AJAX
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'تم الحذف!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'موافق'
                            }).then(() => {
                                // إعادة تحميل الصفحة
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'خطأ!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'موافق'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'حدث خطأ غير متوقع';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            errorMessage = 'الحلقة غير موجودة';
                        } else if (xhr.status === 403) {
                            errorMessage = 'ليس لديك صلاحية لحذف هذه الحلقة';
                        }
                        
                        Swal.fire({
                            title: 'خطأ!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'موافق'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush

