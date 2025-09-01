@extends("layouts.dashboard")

@section("title", "تفاصيل المعلم")

@section("content")
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل المعلم: {{ $teacher->name }}</h1>
        <div>
            <a href="{{ route("teachers.edit", $teacher) }}" class="btn btn-warning">تعديل</a>
            <a href="{{ route("teachers.index") }}" class="btn btn-secondary">العودة</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">المعلومات الأساسية</div>
                <div class="card-body">
                    <p><strong>الاسم:</strong> {{ $teacher->name }}</p>
                    <p><strong>الهاتف:</strong> {{ $teacher->phone ?? "غير محدد" }}</p>
                    <p><strong>البريد الإلكتروني:</strong> {{ $teacher->email ?? "غير محدد" }}</p>
                    <p><strong>التخصص:</strong> {{ $teacher->specialization ?? "غير محدد" }}</p>
                    <p><strong>الحالة:</strong> {{ $teacher->is_active ? "نشط" : "غير نشط" }}</p>
                    <p><strong>تاريخ التوظيف:</strong> {{ $teacher->created_at->format("Y-m-d") }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">الحلقات المسؤول عنها</div>
                <div class="card-body">
                    @if($teacher->circles && $teacher->circles->count() > 0)
                        @foreach($teacher->circles as $circle)
                            <div class="mb-2">
                                <strong>{{ $circle->name }}</strong><br>
                                <small class="text-muted">{{ $circle->description }}</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">لا يوجد حلقات مسندة للمعلم</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
