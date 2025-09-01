@extends('layouts.teacher')

@section('title', 'إدارة الجلسات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary mb-1">إدارة الجلسات</h2>
                    <p class="text-muted mb-0">إدارة وتتبع جلسات الحلقات</p>
                </div>
                <div>
                    <a href="{{ route('teacher.sessions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إنشاء جلسة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        جميع الجلسات
                    </h5>
                </div>
                <div class="card-body">
                    @if($sessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>العنوان</th>
                                    <th>الحلقة</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>الحالة</th>
                                    <th>الحضور</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessions as $session)
                                <tr>
                                    <td>
                                        <strong>{{ $session->title }}</strong>
                                        @if($session->description)
                                        <br><small class="text-muted">{{ Str::limit($session->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $session->circle->name }}</td>
                                    <td>{{ $session->session_date }}</td>
                                    <td>{{ $session->start_time }} - {{ $session->end_time }}</td>
                                    <td>
                                        @switch($session->status)
                                            @case('scheduled')
                                                <span class="badge bg-primary">مجدولة</span>
                                                @break
                                            @case('in_progress')
                                                <span class="badge bg-warning">جارية</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-success">مكتملة</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">ملغية</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($session->attendances->count() > 0)
                                            <span class="badge bg-success">{{ $session->attendances->where('status', 'present')->count() }}</span>
                                            <span class="badge bg-danger">{{ $session->attendances->where('status', 'absent')->count() }}</span>
                                        @else
                                            <span class="text-muted">لم يسجل</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teacher.sessions.show', $session) }}" class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($session->status === 'scheduled')
                                            <a href="{{ route('teacher.sessions.edit', $session) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            <a href="{{ route('teacher.sessions.attendance', $session) }}" class="btn btn-outline-success">
                                                <i class="fas fa-users"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $sessions->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد جلسات</h5>
                        <p class="text-muted">لم يتم إنشاء أي جلسات بعد</p>
                        <a href="{{ route('teacher.sessions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>إنشاء جلسة جديدة
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

