@extends('layouts.dashboard')

@section('title', 'تفاصيل الجلسة')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الجلسة</h1>
        <div>
            <a href="{{ route('sessions.edit', $session) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> تعديل الجلسة
            </a>
            <a href="{{ route('sessions.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Session Details Card -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الجلسة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">عنوان الجلسة:</label>
                            <p class="text-gray-800">{{ $session->session_title ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">تاريخ الجلسة:</label>
                            <p class="text-gray-800">
                                <i class="fas fa-calendar-alt text-primary"></i>
                                {{ $session->session_date ? \Carbon\Carbon::parse($session->session_date)->format('Y-m-d') : 'غير محدد' }}
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">الحلقة:</label>
                            <p>
                                <span class="badge badge-info badge-lg">{{ $session->circle->name ?? 'غير محدد' }}</span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">المعلم:</label>
                            <p class="text-gray-800">
                                <i class="fas fa-user text-success"></i>
                                {{ $session->circle->teacher->name ?? 'غير محدد' }}
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">التوقيت المجدول:</label>
                            <p class="text-gray-800">
                                <i class="fas fa-clock text-info"></i>
                                @if($session->circle && $session->circle->start_time && $session->circle->end_time)
                                    {{ $session->circle->start_time }} - {{ $session->circle->end_time }}
                                @else
                                    غير محدد
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">التوقيت الفعلي:</label>
                            <p class="text-gray-800">
                                <i class="fas fa-clock text-warning"></i>
                                @if($session->actual_start_time && $session->actual_end_time)
                                    {{ $session->actual_start_time }} - {{ $session->actual_end_time }}
                                @else
                                    لم يتم تحديده بعد
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">الحالة:</label>
                            <p>
                                @switch($session->status)
                                    @case('scheduled')
                                        <span class="badge badge-primary badge-lg">مجدولة</span>
                                        @break
                                    @case('ongoing')
                                        <span class="badge badge-warning badge-lg">جارية</span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-success badge-lg">مكتملة</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger badge-lg">ملغية</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary badge-lg">غير محدد</span>
                                @endswitch
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold">تسجيل الحضور:</label>
                            <p>
                                @if($session->attendance_taken)
                                    <span class="badge badge-success badge-lg">
                                        <i class="fas fa-check"></i> تم التسجيل
                                    </span>
                                @else
                                    <span class="badge badge-warning badge-lg">
                                        <i class="fas fa-clock"></i> لم يتم بعد
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($session->session_description)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold">وصف الجلسة:</label>
                            <p class="text-gray-800">{{ $session->session_description }}</p>
                        </div>
                    </div>
                    @endif

                    @if($session->lesson_content)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold">محتوى الدرس:</label>
                            <div class="bg-light p-3 rounded">
                                {{ $session->lesson_content }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($session->homework)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold">الواجب المنزلي:</label>
                            <div class="bg-light p-3 rounded">
                                {{ $session->homework }}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($session->session_notes)
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold">ملاحظات الجلسة:</label>
                            <div class="bg-light p-3 rounded">
                                {{ $session->session_notes }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attendance Statistics Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات الحضور</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <h4 class="text-primary">{{ $session->total_students ?? 0 }}</h4>
                            <small class="text-muted">إجمالي الطلاب</small>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-success">{{ $session->present_students ?? 0 }}</h5>
                                <small class="text-muted">حاضر</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-danger">{{ $session->absent_students ?? 0 }}</h5>
                                <small class="text-muted">غائب</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-info">{{ number_format($session->attendance_percentage ?? 0, 1) }}%</h5>
                                <small class="text-muted">نسبة الحضور</small>
                            </div>
                        </div>

                        @if($session->attendance_taken)
                        <div class="mt-3">
                            <small class="text-muted">
                                تم تسجيل الحضور في: {{ $session->attendance_taken_at ? \Carbon\Carbon::parse($session->attendance_taken_at)->format('Y-m-d H:i') : 'غير محدد' }}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إجراءات سريعة</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$session->attendance_taken && $session->status !== 'cancelled')
                        <a href="{{ route('attendance.session', $session) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-check"></i> تسجيل الحضور
                        </a>
                        @endif
                        
                        @if($session->status === 'scheduled')
                        <a href="{{ route('sessions.edit', $session) }}" class="btn btn-warning btn-sm w-100">
                            <i class="fas fa-edit"></i> تعديل الجلسة
                        </a>
                        @endif

                        @if($session->status === 'ongoing')
                        <a href="{{ route('sessions.edit', $session) }}" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-edit"></i> تعديل الجلسة
                        </a>
                        @endif

                        <a href="{{ route('sessions.edit', $session) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> تعديل الجلسة
                        </a>
                        
                        <form action="{{ route('sessions.destroy', $session) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash"></i> حذف الجلسة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

