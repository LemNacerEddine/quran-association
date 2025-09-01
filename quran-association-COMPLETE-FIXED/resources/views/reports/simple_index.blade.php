@extends('layouts.dashboard')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">التقارير والإحصائيات</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> تصدير التقرير
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- إجمالي الطلاب -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الطلاب</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_students'] }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up"></i> {{ $statistics['active_students'] }} نشط
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- إجمالي المعلمين -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">إجمالي المعلمين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_teachers'] }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up"></i> {{ $statistics['active_teachers'] }} نشط
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- إجمالي الحلقات -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إجمالي الحلقات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_circles'] }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up"></i> {{ $statistics['active_circles'] }} نشط
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- حضور اليوم -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">حضور اليوم</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total_attendances_today'] }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-calendar-check"></i> {{ date('Y-m-d') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- أفضل الحلقات -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">أفضل الحلقات</h6>
                </div>
                <div class="card-body">
                    @if($topCircles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>اسم الحلقة</th>
                                        <th>المعلم</th>
                                        <th>عدد الطلاب</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topCircles as $circle)
                                    <tr>
                                        <td>{{ $circle->name }}</td>
                                        <td>{{ $circle->teacher->name ?? 'غير محدد' }}</td>
                                        <td>{{ $circle->students_count }}</td>
                                        <td>
                                            <span class="badge badge-{{ $circle->is_active ? 'success' : 'secondary' }}">
                                                {{ $circle->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">لا توجد بيانات متاحة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات سريعة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>نسبة الطلاب النشطين</span>
                                <span class="font-weight-bold">
                                    {{ $statistics['total_students'] > 0 ? round(($statistics['active_students'] / $statistics['total_students']) * 100, 1) : 0 }}%
                                </span>
                            </div>
                            <div class="progress mt-1">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $statistics['total_students'] > 0 ? ($statistics['active_students'] / $statistics['total_students']) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>نسبة المعلمين النشطين</span>
                                <span class="font-weight-bold">
                                    {{ $statistics['total_teachers'] > 0 ? round(($statistics['active_teachers'] / $statistics['total_teachers']) * 100, 1) : 0 }}%
                                </span>
                            </div>
                            <div class="progress mt-1">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ $statistics['total_teachers'] > 0 ? ($statistics['active_teachers'] / $statistics['total_teachers']) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>نسبة الحلقات النشطة</span>
                                <span class="font-weight-bold">
                                    {{ $statistics['total_circles'] > 0 ? round(($statistics['active_circles'] / $statistics['total_circles']) * 100, 1) : 0 }}%
                                </span>
                            </div>
                            <div class="progress mt-1">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: {{ $statistics['total_circles'] > 0 ? ($statistics['active_circles'] / $statistics['total_circles']) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- تقارير إضافية -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تقارير مفصلة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="btn btn-outline-primary btn-block disabled">
                                <i class="fas fa-calendar-check"></i> تقرير الحضور
                                <small class="d-block text-muted">قريباً</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="btn btn-outline-success btn-block disabled">
                                <i class="fas fa-chart-line"></i> تقرير التقدم
                                <small class="d-block text-muted">قريباً</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="btn btn-outline-info btn-block disabled">
                                <i class="fas fa-chalkboard-teacher"></i> تقرير المعلمين
                                <small class="d-block text-muted">قريباً</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

