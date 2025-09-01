<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة تحكم المعلم') - جمعية تحفيظ القرآن</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: white !important;
            transform: translateY(-1px);
        }
        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .main-content {
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('teacher.dashboard') }}">
                <i class="fas fa-chalkboard-teacher me-2"></i>
                جمعية تحفيظ القرآن - المعلمين
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teacher.dashboard') }}">
                            <i class="fas fa-home me-1"></i>لوحة التحكم
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teacher.sessions.index') }}">
                            <i class="fas fa-calendar-alt me-1"></i>الجلسات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teacher.profile') }}">
                            <i class="fas fa-user me-1"></i>الملف الشخصي
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ Session::get('teacher_name', 'المعلم') }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('teacher.profile') }}">
                                    <i class="fas fa-user me-2"></i>الملف الشخصي
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('teacher.logout') }}">
                                    <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>

