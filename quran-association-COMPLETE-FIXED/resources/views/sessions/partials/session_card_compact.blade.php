@php
    $sessionDateTime = \Carbon\Carbon::parse($session->session_date . ' ' . $session->start_time);
    $now = \Carbon\Carbon::now();
    $endDateTime = \Carbon\Carbon::parse($session->session_date . ' ' . $session->end_time);
    
    // تحديد حالة الجلسة
    $status = 'upcoming';
    $statusText = 'مجدولة';
    $statusClass = 'primary';
    $statusIcon = 'clock';
    
    if ($session->attendances && $session->attendances->count() > 0) {
        $status = 'completed';
        $statusText = 'مكتملة';
        $statusClass = 'success';
        $statusIcon = 'check-circle';
    } elseif ($endDateTime->isPast()) {
        $status = 'missed';
        $statusText = 'فائتة';
        $statusClass = 'danger';
        $statusIcon = 'exclamation-triangle';
    } elseif ($sessionDateTime->isPast() && $endDateTime->isFuture()) {
        $status = 'live';
        $statusText = 'جارية';
        $statusClass = 'warning';
        $statusIcon = 'play-circle';
    }
    
    // حساب الوقت المتبقي أو المنقضي
    $timeInfo = '';
    if ($status === 'upcoming') {
        $diff = $sessionDateTime->diffForHumans($now);
        $timeInfo = 'خلال ' . str_replace(['في ', 'بعد '], '', $diff);
    } elseif ($status === 'missed') {
        $diff = $endDateTime->diffForHumans($now);
        $timeInfo = 'تأخر ' . str_replace(['منذ ', 'قبل '], '', $diff);
    } elseif ($status === 'live') {
        $timeInfo = 'جارية الآن';
    } elseif ($status === 'completed') {
        $attendanceCount = $session->attendances->where('status', 'present')->count();
        $totalStudents = $session->circle->students->count();
        $attendanceRate = $totalStudents > 0 ? round(($attendanceCount / $totalStudents) * 100) : 0;
        $timeInfo = "نسبة الحضور {$attendanceRate}%";
    }
@endphp

<div class="card border-0 shadow-sm h-100 session-card session-{{ $status }}">
    <div class="card-body p-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="flex-grow-1">
                <h6 class="card-title mb-1 text-truncate" title="{{ $session->title }}">
                    {{ $session->title }}
                </h6>
                <p class="text-muted small mb-0 text-truncate" title="{{ $session->circle->name }}">
                    <i class="fas fa-circle me-1"></i>
                    {{ $session->circle->name }}
                </p>
            </div>
            <span class="badge bg-{{ $statusClass }} ms-2">
                <i class="fas fa-{{ $statusIcon }} me-1"></i>
                {{ $statusText }}
            </span>
        </div>

        <!-- Teacher -->
        <div class="mb-2">
            <small class="text-muted d-flex align-items-center">
                <i class="fas fa-chalkboard-teacher me-2 text-secondary"></i>
                <span class="text-truncate" title="{{ $session->circle->teacher->name }}">
                    {{ $session->circle->teacher->name }}
                </span>
            </small>
        </div>

        <!-- Date & Time -->
        <div class="mb-2">
            <div class="row g-1">
                <div class="col-6">
                    <small class="text-muted d-flex align-items-center">
                        <i class="fas fa-calendar me-1 text-secondary"></i>
                        {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                    </small>
                </div>
                <div class="col-6">
                    <small class="text-muted d-flex align-items-center">
                        <i class="fas fa-clock me-1 text-secondary"></i>
                        {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Status Info -->
        @if($timeInfo)
        <div class="mb-3">
            <small class="text-{{ $statusClass }} fw-bold">
                <i class="fas fa-info-circle me-1"></i>
                {{ $timeInfo }}
            </small>
        </div>
        @endif

        <!-- Actions -->
        <div class="d-flex gap-1">
            @if($status === 'upcoming' || $status === 'live' || $status === 'missed')
                <a href="{{ route('attendance.session', $session->id) }}" 
                   class="btn btn-sm btn-outline-primary flex-fill">
                    <i class="fas fa-user-check me-1"></i>
                    <span class="d-none d-sm-inline">تسجيل الحضور</span>
                </a>
            @endif
            
            <a href="{{ route('sessions.show', $session->id) }}" 
               class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-eye"></i>
            </a>
            
            <a href="{{ route('sessions.edit', $session->id) }}" 
               class="btn btn-sm btn-outline-warning">
                <i class="fas fa-edit"></i>
            </a>
            
            <form action="{{ route('sessions.destroy', $session->id) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.session-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.session-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15) !important;
}

.session-upcoming {
    border-left-color: #0d6efd;
}

.session-live {
    border-left-color: #ffc107;
    animation: pulse 2s infinite;
}

.session-completed {
    border-left-color: #198754;
}

.session-missed {
    border-left-color: #dc3545;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}

.card-title {
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.3;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.text-truncate {
    max-width: 100%;
}
</style>

