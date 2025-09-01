<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'جمعية تحفيظ القرآن الكريم')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Amiri for Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Amiri', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #2c3e50 !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            color: #2c3e50 !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: #667eea !important;
            transform: translateY(-2px);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .stats-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 3rem 0;
            margin-top: 5rem;
        }
        
        .hero-section {
            padding: 5rem 0;
            text-align: center;
            color: white;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .content-section {
            padding: 4rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            color: #2c3e50;
            font-weight: 700;
            font-size: 2.5rem;
        }
        
        .islamic-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M30 30c0-11.046-8.954-20-20-20s-20 8.954-20 20 8.954 20 20 20 20-8.954 20-20zm0 0c0 11.046 8.954 20 20 20s20-8.954 20-20-8.954-20-20-20-20 8.954-20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
    
    @yield('styles')
</head>
<body class="islamic-pattern">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-mosque me-2"></i>
                جمعية تحفيظ القرآن الكريم
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            لوحة التحكم
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('students.index') }}">
                            <i class="fas fa-user-graduate me-1"></i>
                            إدارة الطلاب
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('guardians.index') }}">
                            <i class="fas fa-user-friends me-1"></i>
                            إدارة أولياء الأمور
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teachers.index') }}">
                            <i class="fas fa-chalkboard-teacher me-1"></i>
                            إدارة المعلمين
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('circles.index') }}">
                            <i class="fas fa-users me-1"></i>
                            إدارة الحلقات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('schedules.index') }}">
                            <i class="fas fa-calendar-alt me-1"></i>
                            إدارة الجدولة
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sessions.index') }}">
                            <i class="fas fa-clipboard-list me-1"></i>
                            إدارة الجلسات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('attendance.index') }}">
                            <i class="fas fa-calendar-check me-1"></i>
                            تسجيل الحضور
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-bar me-1"></i>
                            التقارير
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chart-line me-2"></i>تقارير الحضور</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chart-pie me-2"></i>تقارير الأداء</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-alt me-2"></i>التقارير الشهرية</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>
                            النظام
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-bell me-2"></i>الإشعارات</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-users-cog me-2"></i>إدارة المستخدمين</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-tools me-2"></i>الإعدادات</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main style="margin-top: 80px;">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-mosque me-2"></i>جمعية تحفيظ القرآن الكريم</h5>
                    <p>نسعى لخدمة كتاب الله وتعليم القرآن الكريم للجميع بأفضل الطرق والوسائل الحديثة.</p>
                </div>
                <div class="col-md-4">
                    <h5>روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('students.index') }}" class="text-light">الطلاب</a></li>
                        <li><a href="{{ route('teachers.index') }}" class="text-light">المعلمين</a></li>
                        <li><a href="{{ route('circles.index') }}" class="text-light">الحلقات</a></li>
                        <li><a href="{{ route('dashboard') }}" class="text-light">لوحة التحكم</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>تواصل معنا</h5>
                    <p><i class="fas fa-phone me-2"></i>+966 50 123 4567</p>
                    <p><i class="fas fa-envelope me-2"></i>info@quran-association.com</p>
                    <p><i class="fas fa-map-marker-alt me-2"></i>الرياض، المملكة العربية السعودية</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} جمعية تحفيظ القرآن الكريم. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>

