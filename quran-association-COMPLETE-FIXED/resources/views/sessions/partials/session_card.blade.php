<div class="col-lg-4 col-md-6 mb-3">
    <div class="card session-card border-0 shadow-sm h-100 {{ $session->status_info['color_class'] ?? 'bg-light border-secondary' }}">
        <div class="card-body position-relative">
            <!-- Status Badge -->
            <div class="session-status-badge">
                {!! $session->status_info['badge_text'] ?? '<span class="badge bg-secondary">غير محدد</span>' !!}
            </div>

            <!-- Session Icon and Title -->
            <div class="d-flex align-items-start mb-3">
                <div class="flex-shrink-0 me-3">
                    {!! $session->status_info['icon'] ?? '<i class="fas fa-question-circle text-secondary"></i>' !!}
                </div>
                <div class="flex-grow-1">
                    <h6 class="card-title mb-1 fw-bold">
                        {{ $session->session_title ?? 'جلسة غير محددة' }}
                    </h6>
                    <p class="text-muted mb-0 small">
                        {{ $session->circle->name ?? 'حلقة غير محددة' }}
                    </p>
                </div>
            </div>

            <!-- Session Details -->
            <div class="session-details mb-3">
                <!-- Teacher -->
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-user-tie text-muted me-2"></i>
                    <span class="small">{{ $session->circle->teacher->name ?? 'معلم غير محدد' }}</span>
                </div>

                <!-- Date and Time -->
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-calendar text-muted me-2"></i>
                    <span class="small">{{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}</span>
                </div>

                <!-- Time -->
                @if($session->circle && $session->circle->start_time && $session->circle->end_time)
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-clock text-muted me-2"></i>
                    <span class="small">
                        {{ \Carbon\Carbon::parse($session->circle->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($session->circle->end_time)->format('H:i') }}
                    </span>
                </div>
                @endif

                <!-- Time Info -->
                @if(isset($session->status_info['time_info']))
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-info-circle text-muted me-2"></i>
                    <span class="small time-info {{ $session->status_info['status'] === 'live' ? 'text-warning fw-bold' : '' }}">
                        {{ $session->status_info['time_info'] }}
                    </span>
                </div>
                @endif
            </div>

            <!-- Attendance Stats (for completed sessions) -->
            @if(isset($session->status_info['attendance_stats']))
            <div class="attendance-stats mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted">نسبة الحضور</span>
                    <span class="small fw-bold text-success">{{ $session->status_info['attendance_stats']['attendance_rate'] }}%</span>
                </div>
                <div class="progress attendance-progress">
                    <div class="progress-bar bg-success" 
                         style="width: {{ $session->status_info['attendance_stats']['attendance_rate'] }}%"></div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span class="small text-success">
                        <i class="fas fa-check me-1"></i>
                        {{ $session->status_info['attendance_stats']['present'] }} حاضر
                    </span>
                    <span class="small text-danger">
                        <i class="fas fa-times me-1"></i>
                        {{ $session->status_info['attendance_stats']['absent'] }} غائب
                    </span>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="session-actions">
                @if(isset($session->status_info['action_button']))
                    @php $button = $session->status_info['action_button']; @endphp
                    
                    @if($session->status_info['status'] === 'completed')
                        <a href="{{ route('attendance.session', $session->id) }}" 
                           class="{{ $button['class'] }} w-100">
                            <i class="{{ $button['icon'] }} me-1"></i>
                            {{ $button['text'] }}
                        </a>
                    @elseif(in_array($session->status_info['status'], ['missed', 'live']))
                        <a href="{{ route('attendance.session', $session->id) }}" 
                           class="{{ $button['class'] }} w-100">
                            <i class="{{ $button['icon'] }} me-1"></i>
                            {{ $button['text'] }}
                        </a>
                    @else
                        <a href="{{ route('sessions.show', $session->id) }}" 
                           class="{{ $button['class'] }} w-100">
                            <i class="{{ $button['icon'] }} me-1"></i>
                            {{ $button['text'] }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('sessions.show', $session->id) }}" 
                       class="btn btn-outline-secondary btn-sm w-100">
                        <i class="fas fa-eye me-1"></i>
                        عرض التفاصيل
                    </a>
                @endif

                <!-- Secondary Actions -->
                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('sessions.edit', $session->id) }}" 
                       class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="fas fa-edit me-1"></i>
                        تعديل
                    </a>
                    
                    @if($session->status_info['status'] !== 'completed')
                    <form action="{{ route('sessions.destroy', $session->id) }}" 
                          method="POST" 
                          class="flex-fill"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="fas fa-trash me-1"></i>
                            حذف
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

