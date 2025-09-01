@extends("layouts.dashboard")

@section("title", "تعديل الطالب")

@section("content")
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل الطالب: {{ $student->name }}</h1>
        <a href="{{ route("students.index") }}" class="btn btn-secondary">العودة</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route("students.update", $student) }}">
                @csrf
                @method("PUT")
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name">الاسم</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old("name", $student->name) }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone">رقم الهاتف</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old("phone", $student->phone) }}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="birth_date">تاريخ الميلاد</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="gender">الجنس</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="male" {{ old("gender", $student->gender) == "male" ? "selected" : "" }}>ذكر</option>
                                <option value="female" {{ old("gender", $student->gender) == "female" ? "selected" : "" }}>أنثى</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old("is_active", $student->is_active) ? "checked" : "" }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    <a href="{{ route("students.show", $student) }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
