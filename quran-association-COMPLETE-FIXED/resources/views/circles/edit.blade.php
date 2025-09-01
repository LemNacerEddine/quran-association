@extends("layouts.dashboard")

@section("title", "تعديل الحلقة")

@section("content")
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تعديل الحلقة: {{ $circle->name }}</h1>
        <a href="{{ route("circles.index") }}" class="btn btn-secondary">العودة</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route("circles.update", $circle) }}">
                @csrf
                @method("PUT")
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name">اسم الحلقة</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old("name", $circle->name) }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="teacher_id">المعلم</label>
                            <select class="form-control" id="teacher_id" name="teacher_id">
                                <option value="">اختر المعلم</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old("teacher_id", $circle->teacher_id) == $teacher->id ? "selected" : "" }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="description">الوصف</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old("description", $circle->description) }}</textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="level">المستوى</label>
                            <select class="form-control" id="level" name="level">
                                <option value="beginner" {{ old("level", $circle->level) == "beginner" ? "selected" : "" }}>مبتدئ</option>
                                <option value="intermediate" {{ old("level", $circle->level) == "intermediate" ? "selected" : "" }}>متوسط</option>
                                <option value="advanced" {{ old("level", $circle->level) == "advanced" ? "selected" : "" }}>متقدم</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="start_time">وقت البداية</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old("start_time", $circle->start_time) }}">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="end_time">وقت النهاية</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old("end_time", $circle->end_time) }}">
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label>أيام الحلقة</label>
                    <div class="row">
                        @php
                            $days = [
                                'sunday' => 'الأحد',
                                'monday' => 'الاثنين', 
                                'tuesday' => 'الثلاثاء',
                                'wednesday' => 'الأربعاء',
                                'thursday' => 'الخميس',
                                'friday' => 'الجمعة',
                                'saturday' => 'السبت'
                            ];
                            
                            // تحويل الأيام العربية إلى إنجليزية للمقارنة
                            $arabicToEnglish = [
                                'الأحد' => 'sunday',
                                'الاثنين' => 'monday', 
                                'الثلاثاء' => 'tuesday',
                                'الأربعاء' => 'wednesday',
                                'الخميس' => 'thursday',
                                'الجمعة' => 'friday',
                                'السبت' => 'saturday'
                            ];

                            $circleDays = $circle->days ? explode(',', $circle->days) : [];
                            $selectedDays = [];
                            foreach($circleDays as $day) {
                                $day = trim($day);
                                if (isset($arabicToEnglish[$day])) {
                                    $selectedDays[] = $arabicToEnglish[$day];
                                } else {
                                    $selectedDays[] = $day; // إذا كان إنجليزي أصلاً
                                }
                            }
                            $selectedDays = old("days", $selectedDays);
                        @endphp
                        @foreach($days as $key => $day)
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="day_{{ $key }}" name="days[]" value="{{ $key }}" 
                                           {{ in_array($key, $selectedDays) ? "checked" : "" }}>
                                    <label class="form-check-label" for="day_{{ $key }}">{{ $day }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="location">الموقع</label>
                            <input type="text" class="form-control" id="location" name="location" value="{{ old("location", $circle->location) }}">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="max_students">الحد الأقصى للطلاب</label>
                            <input type="number" class="form-control" id="max_students" name="max_students" value="{{ old("max_students", $circle->max_students) }}">
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old("is_active", $circle->is_active) ? "checked" : "" }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    <a href="{{ route("circles.show", $circle) }}" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
