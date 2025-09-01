@extends('layouts.teacher')

@section('title', 'تفاصيل الجلسة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary mb-1">{{ $session->title }}</h2>
                    <p class="text-muted mb-0">{{ $session->circle->name }} - {{ $session->session_date->format('Y-m-d') }}</p>
                </div>
                <div>
                    <a href="{{ route('teacher.sessions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للجلسات
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Info -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات الجلسة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>الحلقة:</strong>
                            <p class="mb-0">{{ $session->circle->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>التاريخ:</strong>
                            <p class="mb-0">{{ $session->session_date->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>الوقت:</strong>
                            <p class="mb-0">{{ $session->start_time }} - {{ $session->end_time }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>الحالة:</strong>
                            <span class="badge bg-{{ $session->status_color }}">{{ $session->status_text }}</span>
                        </div>
                        @if($session->description)
                        <div class="col-12 mb-3">
                            <strong>الوصف:</strong>
                            <p class="mb-0">{{ $session->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        إجراءات الجلسة
                    </h5>
                </div>
                <div class="card-body">
                    @if($session->status === 'scheduled')
                    <form action="{{ route('teacher.sessions.start', $session) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-play me-2"></i>بدء الجلسة
                        </button>
                    </form>
                    @endif

                    @if($session->status === 'in_progress')
                    <form action="{{ route('teacher.sessions.end', $session) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-stop me-2"></i>إنهاء الجلسة
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('teacher.sessions.attendance', $session) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-users me-2"></i>إدارة الحضور
                    </a>

                    @if($session->status === 'scheduled')
                    <a href="{{ route('teacher.sessions.edit', $session) }}" class="btn btn-outline-warning w-100 mb-2">
                        <i class="fas fa-edit me-2"></i>تعديل الجلسة
                    </a>
                    @endif

                    @if($session->status !== 'completed')
                    <form action="{{ route('teacher.sessions.destroy', $session) }}" method="POST" 
                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-2"></i>حذف الجلسة
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Students and Attendance -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        الطلاب والحضور
                    </h5>
                </div>
                <div class="card-body">
                    @if($session->circle->students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الطالب</th>
                                    <th>الحضور</th>
                                    <th>النقاط</th>
                                    <th>الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session->circle->students as $student)
                                @php
                                    $attendance = $session->attendances->where('student_id', $student->id)->first();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $student->name }}</strong>
                                        <br><small class="text-muted">{{ $student->student_id }}</small>
                                    </td>
                                    <td>
                                        @if($attendance)
                                            <span class="badge bg-{{ $attendance->status_color }}">
                                                <i class="{{ $attendance->status_icon }} me-1"></i>
                                                {{ $attendance->status_text }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">لم يسجل</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance && $attendance->points)
                                            <span class="badge bg-info">{{ $attendance->points }} نقطة</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance && $attendance->notes)
                                            <small>{{ $attendance->notes }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Attendance Summary -->
                    @if($session->attendances->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ $session->attendances->where('status', 'present')->count() }}</h4>
                                <small class="text-muted">حاضر</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-danger">{{ $session->attendances->where('status', 'absent')->count() }}</h4>
                                <small class="text-muted">غائب</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ $session->attendances->where('status', 'late')->count() }}</h4>
                                <small class="text-muted">متأخر</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info">{{ $session->attendances->where('status', 'excused')->count() }}</h4>
                                <small class="text-muted">غياب بعذر</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا يوجد طلاب في هذه الحلقة</h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

