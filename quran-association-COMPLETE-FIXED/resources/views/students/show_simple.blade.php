<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الطالب - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>تفاصيل الطالب</h3>
                    </div>
                    <div class="card-body">
                        <h4>{{ $student->name }}</h4>
                        <p><strong>الهاتف:</strong> {{ $student->phone ?? 'غير محدد' }}</p>
                        <p><strong>الجنس:</strong> {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
                        <p><strong>الحالة:</strong> {{ $student->is_active ? 'نشط' : 'غير نشط' }}</p>
                        <p><strong>تاريخ التسجيل:</strong> {{ $student->created_at->format('Y-m-d') }}</p>
                        
                        <div class="mt-3">
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">العودة</a>
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">تعديل</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

