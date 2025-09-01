@extends('layouts.teacher')

@section('title', 'الملف الشخصي')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>
                    الملف الشخصي
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">المعلومات الأساسية</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>الاسم:</strong></td>
                                <td>{{ $teacher->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>رقم الهاتف:</strong></td>
                                <td>{{ $teacher->phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>البريد الإلكتروني:</strong></td>
                                <td>{{ $teacher->email ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>تاريخ الميلاد:</strong></td>
                                <td>{{ $teacher->birth_date ? $teacher->birth_date->format('Y-m-d') : 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>الجنس:</strong></td>
                                <td>{{ $teacher->gender == 'male' ? 'ذكر' : ($teacher->gender == 'female' ? 'أنثى' : 'غير محدد') }}</td>
                            </tr>
                            <tr>
                                <td><strong>العنوان:</strong></td>
                                <td>{{ $teacher->address ?? 'غير محدد' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-muted">المعلومات المهنية</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>المؤهل:</strong></td>
                                <td>{{ $teacher->qualification ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>سنوات الخبرة:</strong></td>
                                <td>{{ $teacher->experience ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>التخصص:</strong></td>
                                <td>{{ $teacher->specialization ?? 'غير محدد' }}</td>
                            </tr>
                            <tr>
                                <td><strong>الحالة:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $teacher->is_active ? 'success' : 'secondary' }}">
                                        {{ $teacher->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>آخر دخول:</strong></td>
                                <td>{{ $teacher->last_login_at ? $teacher->last_login_at->format('H:i d-m-Y') : 'أول مرة' }}</td>
                            </tr>
                            <tr>
                                <td><strong>كود الدخول:</strong></td>
                                <td>
                                    <code class="bg-light p-1 rounded">{{ $teacher->password }}</code>
                                    <small class="text-muted d-block">آخر 4 أرقام من الهاتف</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <h6 class="text-muted mb-3">الحلقات المسؤول عنها</h6>
                @if($teacher->circles->count() > 0)
                    <div class="row">
                        @foreach($teacher->circles as $circle)
                        <div class="col-md-6 mb-3">
                            <div class="card border-start border-primary border-4">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">{{ $circle->name }}</h6>
                                    <p class="card-text text-muted mb-1">
                                        <i class="fas fa-layer-group me-1"></i>
                                        المستوى: {{ $circle->level ?? 'غير محدد' }}
                                    </p>
                                    <p class="card-text text-muted mb-1">
                                        <i class="fas fa-users me-1"></i>
                                        عدد الطلاب: {{ $circle->students->count() }}
                                    </p>
                                    <p class="card-text text-muted mb-0">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        المكان: {{ $circle->location ?? 'غير محدد' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        لا توجد حلقات مسندة إليك حالياً
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

