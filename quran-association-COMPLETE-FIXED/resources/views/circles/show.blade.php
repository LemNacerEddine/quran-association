@extends("layouts.dashboard")

@section("title", "تفاصيل الحلقة")

@section("content")
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الحلقة: {{ $circle->name }}</h1>
        <div>
            <a href="{{ route("circles.edit", $circle) }}" class="btn btn-warning">تعديل</a>
            <a href="{{ route("circles.index") }}" class="btn btn-secondary">العودة</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">معلومات الحلقة</div>
                <div class="card-body">
                    <p><strong>اسم الحلقة:</strong> {{ $circle->name }}</p>
                    <p><strong>الوصف:</strong> {{ $circle->description ?? "غير محدد" }}</p>
                    <p><strong>المستوى:</strong> {{ $circle->level ?? "غير محدد" }}</p>
                    <p><strong>الموقع:</strong> {{ $circle->location ?? "غير محدد" }}</p>
                    <p><strong>الأيام:</strong> {{ $circle->schedule_days ?? "غير محدد" }}</p>
                    <p><strong>الوقت:</strong> 
                        @if($circle->start_time && $circle->end_time)
                            {{ $circle->start_time }} - {{ $circle->end_time }}
                        @else
                            غير محدد
                        @endif
                    </p>
                    <p><strong>الحد الأقصى:</strong> {{ $circle->max_students ?? "غير محدد" }}</p>
                    <p><strong>الحالة:</strong> {{ $circle->is_active ? "نشط" : "غير نشط" }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">المعلم والطلاب</div>
                <div class="card-body">
                    @if($circle->teacher)
                        <h6>المعلم المسؤول:</h6>
                        <p><strong>{{ $circle->teacher->name }}</strong><br>
                        <small class="text-muted">{{ $circle->teacher->specialization ?? "غير محدد" }}</small></p>
                    @else
                        <p class="text-muted">لا يوجد معلم مسند للحلقة</p>
                    @endif
                    
                    <hr>
                    
                    <h6>الطلاب المسجلين:</h6>
                    @if($circle->students && $circle->students->count() > 0)
                        @foreach($circle->students as $student)
                            <div class="mb-1">
                                <strong>{{ $student->name }}</strong>
                                <small class="text-muted">({{ $student->phone }})</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">لا يوجد طلاب مسجلين في الحلقة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
